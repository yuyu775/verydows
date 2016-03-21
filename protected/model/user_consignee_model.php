<?php
class user_consignee_model extends Model
{
    public $table_name = 'user_consignee';
    
    public $rules = array
    (
        'name' => array
        (
            'is_required' => array(TRUE, '收件人姓名不能为空'),
            'max_length' => array(60, '收件人姓名不能超过60个字符(30个中文字符)'),
        ),
        'address' => array
        (
            'is_required' => array(TRUE, '详细地址不能为空'),
            'max_length' => array(240, '详细地址不能超过240个字符(120个中文字符)'),
        ),  
        'zip' => array
        (
            'is_digit' => array(TRUE, '邮编必须由数字组成'),
            'max_length' => array(6, '邮编不能超过6个字符'),
        ),
        'mobile_no' => array
        (
            'is_moblie_no' => array(TRUE, '手机号码格式不正确'),
        ),
        'tel_no' => array
        (
            'max_length' => array(20, '固定电话不能超过20个字符'),
        ),
    );
    
    /**
     * 获取用户收件人地址列表
     */
    public function get_user_consignee_list($user_id)
    {
        if($consignee_list = $this->find_all(array('user_id' => $user_id), 'is_default DESC, id DESC'))
        {
            $area = new area();
            foreach($consignee_list as $v)
            {
                $v['area'] = $area->get_area_name($v['province'], $v['city'], $v['borough']);
                $results[] = $v;
            }
            return $results;
        }
        return $consignee_list;
    }
    
    /**
     * 设置订单收件人信息
     */
    public function set_order_consignee($id)
    {
        $row = $this->find(array('id' => $id));
        $area = new area();
        $area_vals = $area->get_area_name($row['province'], $row['city'], $row['borough']);
        $consignee = array_merge($row, $area_vals);
        return $consignee;
    }

}