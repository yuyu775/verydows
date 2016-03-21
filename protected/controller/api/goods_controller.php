<?php
class goods_controller extends Controller
{  
    public function action_reviews()
    {
        $goods_id = vds_request('id');
        $results = array('status' => 0);
        $review_model = new goods_review_model();
        if($GLOBALS['cfg']['user_review_approve'] == 1)
        {
            $total = $review_model->find_count(array('goods_id' => $goods_id, 'status' => 1));
            $where_status = "AND a.status = 1";
        }
        else
        {
            $total = $review_model->find_count(array('goods_id' => $goods_id));
            $where_status = '';
        }
        if($total > 0)
        {    
            $review_model->pager(vds_request('page'), $GLOBALS['cfg']['goods_review_per_num'], 10, $total);
            $limit = $review_model->pager_section();
            $sql = "SELECT a.review_id, a.rating, a.content, a.created_date, a.replied,
                           b.user_id, b.username,
                           c.avatar
                    FROM {$review_model->table_name} AS a
                    LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user AS b
                    ON a.user_id = b.user_id
                    LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user_profile AS c
                    ON b.user_id = c.user_id
                    WHERE a.goods_id = :goods_id {$where_status}
                    ORDER BY a.created_date DESC
                    {$limit}
                   ";
                   
            $binds = array(':goods_id' => $goods_id);
            $reviews = $review_model->query($sql, $binds);
            $ratingmap = $review_model->rating_map;
            foreach($reviews as $k => $v)
            {
                $reviews[$k]['username'] = substr($v['username'], 0, 2) . '***' . substr($v['username'], -2, 2);
                $reviews[$k]['satisficing'] = $ratingmap[$v['rating']];
                $reviews[$k]['created_date'] = date('Y-m-d H:i:s', $v['created_date']);
                if(!empty($v['replied']))
                {
                    $replied = json_decode($v['replied'], TRUE);
                    $replied['dateline'] = date('Y-m-d H:i:s', $replied['dateline']);
                    $reviews[$k]['replied'] = $replied;
                }
            }
            
            $sql = "SELECT AVG(rating) AS avg
                    FROM {$review_model->table_name}
                    WHERE goods_id = :goods_id
                    GROUP BY goods_id
                   ";
            $avgrating = $review_model->query($sql, $binds);
            $results = array
            (
                'status' => 1,
                'total' => $total,
                'rating' => number_format($avgrating[0]['avg'], 1),
                'reviews' => $reviews,
                'paging' => $review_model->page,
            );
        }
        echo json_encode($results);
    }
}