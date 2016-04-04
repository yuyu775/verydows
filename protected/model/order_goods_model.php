<?php
class order_goods_model extends Model
{
    public $table_name = 'order_goods';
     
    /**
     * 添加订单商品数据
     */
    public function add_records($order_id, $goods_list)
    {
        $goods_model = new goods_model();
        foreach($goods_list as $v)
        {
            $data = array
            (
                'order_id' => $order_id,
                'goods_id' => $v['goods_id'],
                'goods_name' => $v['goods_name'],
                'goods_image' => $v['goods_image'],
                'goods_price' => $v['now_price'],
                'goods_opts' => !empty($v['opts']) ? json_encode($v['opts']) : '',
                'goods_qty' => $v['qty'],
            );
            //同时减除商品库存
            if($this->create($data)) $goods_model->decr(array('goods_id' => $v['goods_id']), 'stock_qty', $v['qty']);
        }
    }
    
    /**
     * 重置商品库存
     */
    public function restocking($order_id, $method = 'incr')
    {
        $qty_list = $this->find_all(array('order_id' => $order_id), null, 'goods_id, goods_qty');
        $goods_model = new goods_model();
        foreach($qty_list as $v) $goods_model->$method(array('goods_id' => $v['goods_id']), 'stock_qty', $v['goods_qty']);
    }
    
    public function get_goods_list($order_id)
    {
        if($results = $this->find_all(array('order_id' => $order_id)))
        {
            foreach($results as $k => $v) $results[$k]['goods_opts'] = !empty($v['goods_opts']) ? json_decode($v['goods_opts'], TRUE) : array();
        }
        return $results;
    }
}