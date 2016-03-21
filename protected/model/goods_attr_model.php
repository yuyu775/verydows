<?php
class goods_attr_model extends Model
{
    public $table_name = 'goods_attr';
    
    /**
     * 获取商品属性及属性可选值
     * @param  $cate_id  分类ID
     * @param  $goods_id 商品ID
     */
    public function get_goods_attrs($cate_id, $goods_id)
    {
        $cate_attr_model = new goods_cate_attr_model();
        $sql = "SELECT a.attr_id, a.name, a.opts, a.uom, b.value
                FROM {$cate_attr_model->table_name} AS a
                LEFT JOIN (SELECT * FROM {$this->table_name} WHERE goods_id = :goods_id) AS b
                ON a.attr_id = b.attr_id
                WHERE a.cate_id = :cate_id
                ORDER BY a.seq ASC
               ";   
        if($results = $this->query($sql, array(':cate_id' => $cate_id, ':goods_id' => $goods_id)))
        {
            foreach($results as $k => $v)
            {
                if(!empty($v['opts'])) $results[$k]['opts'] = json_decode($v['opts'], TRUE);
                if($v['value'] === null) $results[$k]['value'] = '';
            }
        }
            
        return $results;
    }
    
    /**
     * 获取商品属性规格参数
     * @param  $cate_id  分类ID
     * @param  $goods_id 商品ID
     */
    public function get_goods_specs($cate_id, $goods_id)
    {
        $cate_attr_model = new goods_cate_attr_model();
        $sql = "SELECT a.name, a.uom, b.value
                FROM {$cate_attr_model->table_name} AS a
                LEFT JOIN (SELECT * FROM {$this->table_name} WHERE goods_id = :goods_id) AS b
                ON a.attr_id = b.attr_id
                WHERE a.cate_id = :cate_id
                ORDER BY a.seq ASC
               ";
        return $this->query($sql, array(':cate_id' => $cate_id, ':goods_id' => $goods_id));
    }
}