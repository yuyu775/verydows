<?php
class goods_review_controller extends general_controller
{
    public function action_index()
    {
        $goods_id = vds_request('goods_id', '');
        $user_id = vds_request('user_id', '');
        if(vds_request('step') == 'search')
        {
            $status = vds_request('status', '');
            $replied = vds_request('replied', '');
            
            $where_1 = $where_2 = ' WHERE 1';
            $binds = array();
            
            if($goods_id != '')
            {
                $where_1 .= ' AND goods_id = :goods_id';
                $where_2 .= ' AND a.goods_id = :goods_id';
                $binds[':goods_id'] = $goods_id;
            }
            if($user_id != '')
            {
                $where_1 .= ' AND user_id = :user_id';
                $where_2 .= ' AND a.user_id = :user_id';
                $binds[':user_id'] = $user_id;
            }
            if($status != '')
            {
                $where_1 .= ' AND status = :status';
                $where_2 .= ' AND a.status = :status';
                $binds[':status'] = $status;
            }
            if($replied == 1)
            {
                $where_1 .= ' AND replied = ""';
                $where_2 .= ' AND a.replied = ""';
            }
            elseif($replied == 2)
            {
                $where_1 .= ' AND replied <> ""';
                $where_2 .= ' AND a.replied <> ""';
            }

            $review_model = new goods_review_model();
            $total = $review_model->query("SELECT COUNT(*) as count FROM {$review_model->table_name} {$where_1}", $binds);
            if($total[0]['count'] > 0)
            {
                $sort_id = vds_request('sort_id', 0, 'post');
                $sortmap = array('review_id DESC', 'created_date DESC', 'created_date ASC', 'rating DESC', 'rating ASC');
                $sort = isset($sortmap[$sort_id])? $sortmap[$sort_id] : $sortmap[0];
                
                $review_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
                $limit = $review_model->pager_section();
                
                $sql = "SELECT a.review_id, a.order_id, a.rating, a.content, a.created_date, a.status, a.replied,
                               b.goods_id, b.goods_name,
                               c.user_id, c.username
                        FROM {$review_model->table_name} AS a
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}order_goods AS b
                        ON a.order_id = b.order_id AND a.goods_id = b.goods_id
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user AS c
                        ON c.user_id = a.user_id
                        {$where_2}
                        ORDER BY {$sort} {$limit}
                       ";
                       
                $results = array
                (
                    'status' => 1,
                    'review_list' => $review_model->query($sql, $binds),
                    'paging' => $review_model->page,
                );
            }
            else
            {
                $results = array('status' => 0);
            }
            
            echo json_encode($results);
        }
        else
        {
            if(!empty($goods_id))
            {
                $goods_model = new goods_model();
                $sql = "SELECT a.goods_id, a.goods_name, a.goods_image,
                               COUNT(b.review_id) AS count, 
                               AVG(b.rating) AS rating
                        FROM {$goods_model->table_name} AS a
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}goods_review AS b
                        ON a.goods_id = b.goods_id
                        WHERE a.goods_id = :goods_id
                        GROUP BY b.goods_id
                       ";
                if($goods = $goods_model->query($sql, array(':goods_id' => $goods_id))) $this->goods = $goods[0];
            }
            if(!empty($user_id))
            {
                $user_model = new user_model();
                $this->user = $user_model->find(array('user_id' => $user_id), null, 'user_id, username, email');
            }
            
            $this->tpl_display('goods/review_list.html');
        }
    }
    
    public function action_view()
    {
        $id = vds_request('id');
        $review_model = new goods_review_model();
        $sql = "SELECT a.review_id, a.order_id, a.rating, a.content, a.created_date, a.status, a.replied,
                       b.goods_id, b.goods_name, b.goods_image,
                       c.user_id, c.username, c.email
                FROM {$review_model->table_name} AS a
                LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}order_goods AS b
                ON a.order_id = b.order_id AND a.goods_id = b.goods_id
                LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user AS c
                ON a.user_id = c.user_id
                WHERE a.review_id = :id
                LIMIT 1
               ";
        
        if($row = $review_model->query($sql, array(':id' => $id)))
        {
            if(!empty($row[0]['replied'])) $row[0]['replied'] = json_decode($row[0]['replied'], TRUE);
            $this->rs = $row[0];
            $this->rating_map = $review_model->rating_map;
            $this->tpl_display('goods/review.html');
        }
        else
        {
            $this->prompt('error', '未找到相应的评价记录');
        }
    }
    
    public function action_approval()
    {
        $id = vds_request('id');
        $status = vds_request('status', 0);
        $review_model = new goods_review_model();
        if(is_array($id))
        {
            $affected = 0;
            foreach($id as $v) $affected += $review_model->update(array('review_id' => $v), array('status' => $status));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功审核 {$affected} 条评价, 失败 {$failure} 条");
        }
        else
        {
            $review_model->update(array('review_id' => $id), array('status' => $status));
            vds_jump(url($this->MOD.'/goods_review', 'view', array('id' => $id)));
        }
    }
    
    public function action_reply()
    {
        $id = vds_request('id');
        $data = array
        (
            'admin' => $_SESSION['admin']['username'],
            'content' => vds_request('content', '', 'post'),
            'dateline' => $_SERVER['REQUEST_TIME'],
        );
        
        $review_model = new goods_review_model();
        if($data['content'] != '')
        {
            if($review_model->update(array('review_id' => $id), array('replied' => json_encode($data))) > 0)
                $this->prompt('success', '回复评价成功', url($this->MOD.'/goods_review', 'view', array('id' => $id)));
            else
                $this->prompt('error', '回复评价失败', url($this->MOD.'/goods_review', 'view', array('id' => $id)));
        }
        else
        {
            $review_model->update(array('review_id' => $id), array('replied' => ''));
            $this->prompt('success', '回复被清除', url($this->MOD.'/goods_review', 'view', array('id' => $id)));
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(!empty($id))
        {
            $review_model = new goods_review_model();
            if(is_array($id))
            {
                $affected = 0;
                foreach($id as $v)
                {
                    $condition = array('review_id' => $v);
                    $affected += $review_model->delete($condition);
                }
                $failure = count($id) - $affected;
                $this->prompt('default', "成功删除 {$affected} 条评价, 失败 {$failure} 条");
            }
            else
            {
                if($review_model->delete(array('review_id' => $id)) > 0) 
                    $this->prompt('success', "删除评价成功", url($this->MOD.'/goods_review', 'index'));
                else 
                    $this->prompt('error', "删除评价失败");
            }
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    } 
}