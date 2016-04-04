<?php
class goods_optional_model extends Model
{
    public $table_name = 'goods_optional';
    
    public function add_goods_optional($goods_id, $opts)
    {   
        foreach($opts['type'] as $k => $v)
        {
            $type_id = intval($v);
            if(!empty($type_id))
            {
                $data = array
                (
                    'goods_id' => $goods_id,
                    'type_id' => $type_id,
                    'opt_text' => trim($opts['text'][$k]),
                    'opt_price' => floatval($opts['price'][$k]),
                );
                $this->create($data);
            }
        }
    }
    
    public function get_goods_optional($goods_id)
    {
        if($find_all = $this->find_all(array('goods_id' => $goods_id)))
        {
            $type_map = $GLOBALS['instance']['cache']->goods_optional_type_model('indexed_list');
            $res = array();
            foreach($find_all as $v)
            {
                $res[$v['type_id']]['type_id'] = $v['type_id'];
                $res[$v['type_id']]['type_name'] = isset($type_map[$v['type_id']]) ? $type_map[$v['type_id']] : 'Unknown';
                $res[$v['type_id']]['children'][] = $v;
            }
            return $res;
        }
        return $find_all;
    }
}