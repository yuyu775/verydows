<?php
class goods_review_model extends Model
{
    public $table_name = 'goods_review';
    
    public $rules = array
    (
        'content' => array
        (
            'is_required' => array(TRUE, '评价内容不能为空'),
            'min_length' => array(10, '评价内容不能少于10个字符'),
            'max_length' => array(800, '评价内容不能超过800个字符'),
        ),
    );
    
    public $addrules = array
    (
        'rating' => array
        (
            'addrule_rating_format' => '请选择1-5分之间的商品评分',
        ),
    );
    
    //自定义验证器：检查评分等级(只能是1-5的数字)
    public function addrule_rating_format($val)
    {
        return preg_match('/[1-5]/', $val) != 0;
    }
    
    public $rating_map = array
    (
        1 => '很不满意',
        2 => '不满意',
        3 => '一般',
        4 => '满意',
        5 => '非常满意',
    );
    
    /**
     * 获取用户评价
     */
    public function get_user_reviews($user_id, $page_id)
    {
        $results = array();
        $total = $this->find_count(array('user_id' => $user_id));
        if($total > 0)
        {
            $this->pager($page_id, 10, 10, $total);
            $limit = $this->pager_section();
            
            $sql = "SELECT a.review_id, a.rating, a.content, a.created_date,
                           b.goods_id, b.goods_name, b.goods_image
                    FROM {$this->table_name} AS a
                    LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}order_goods AS b
                    ON a.goods_id = b.goods_id AND a.order_id = b.order_id
                    WHERE a.user_id = {$user_id}
                    ORDER BY a.created_date DESC
                    {$limit}
                   ";
                   
            $results = $this->query($sql);
        }
        return $results;
    }
}