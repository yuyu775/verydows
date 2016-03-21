<?php
class user_favorite_model extends Model
{
    public $table_name = 'user_favorite';
    
    /**
     * 获取用户最新商品收藏列表
     * @param  $user_id  用户ID
     * @param  $limit 显示数量
     */
    public function get_user_latest_favorites($user_id, $limit = 5)
    {
        $goods_model = new goods_model();
        $sql = "SELECT a.id, a.created_date, b.goods_id, b.goods_name, b.now_price, b.goods_image
                FROM {$this->table_name} AS a
                LEFT JOIN {$goods_model->table_name} AS b
                ON a.goods_id = b.goods_id
                WHERE a.user_id = :user_id
                ORDER BY a.created_date DESC
                LIMIT {$limit}
               ";
               
        return $this->query($sql, array(':user_id' => $user_id));
    }
    
    /**
     * 获取用户全部商品收藏列表
     * @param  $user_id  用户ID
     */
    public function get_user_favorites($user_id, $page_id)
    {   
        $total = $this->find_count(array('user_id' => $user_id));
        if($total > 0)
        {
            $this->pager($page_id, 10, 10, $total);
            $limit = $this->pager_section();

            $goods_model = new goods_model();
            $sql = "SELECT a.id, a.created_date, b.goods_id, b.goods_name, b.now_price, b.goods_image
                    FROM {$this->table_name} AS a
                    LEFT JOIN {$goods_model->table_name} AS b
                    ON a.goods_id = b.goods_id
                    WHERE a.user_id = :user_id
                    ORDER BY a.created_date DESC
                    {$limit}
                   ";
                   
            return $this->query($sql, array(':user_id' => $user_id));
        }

        return array();
    }
}