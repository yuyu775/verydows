<?php
class user_profile_model extends Model
{
    public $table_name = 'user_profile';
    
    public $rules = array
    (
        'name' => array
        (
            'is_required' => array(TRUE, '姓名/称呼不能为空'),
            'max_length' => array(30, '姓名/称呼不能超过30个字符'),
        ),
        'mobile_no' => array
        (
            'is_moblie_no' => array(TRUE, '手机号码格式不正确'),
        ),
        'signature' => array
        (
            'max_length' => array(240, '个性签名不能超过240个字符'),
        )
    );
    
    public $addrules = array
    (
        'qq' => array('addrule_qq_format' => 'QQ号码格式不正确')
    );
    
    //自定义验证器：检查QQ号码格式
    public function addrule_qq_format($val)
    {
        return preg_match('/^$|^[1-9][0-9]{4,12}$/', $val) != 0;
    }

}