<?php
class shipping_method_model extends Model
{
    public $table_name = 'shipping_method';
    
    public $rules = array
    (
        'name' => array
        (
            'is_required' => array(TRUE, '名称不能为空'),
            'max_length' => array(100, '名称不能超过100个字符(50个中文字符)'),
        ),
        'instruction' => array
        (
            'max_length' => array(240, '说明不能超过240个字符(120个中文字符)'),
        ),
        'seq' => array
        (
            'is_seq' => array(TRUE, '排序必须为0-99之间的整数'),
        ),
    );
    
    public $addrules = array
    (
        'params' => array
        (
            'addrule_params_required' => '配送范围不能为空',
        ),
    );
    
    //自定义验证器：检查配送范围参数是否为空
    public function addrule_params_required($val)
    {
        return count(json_decode($val, TRUE)) > 0;
    }
    
    /**
     * 启用的配送方式列表(以id作为数据列表索引)
     */
    public function indexed_list()
    {
        if($find_all = $this->find_all(array('enable' => 1), 'seq ASC')) return vds_array_column($find_all, null, 'id');
        return $find_all;
    }
}
