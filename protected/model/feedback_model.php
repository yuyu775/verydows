<?php
class feedback_model extends Model
{
    public $table_name = 'feedback';

    public $type_map = array('其他', '商品', '活动', '售后', '投诉');
    
    public $status_map = array('已关闭', '进行中', '已完成');
    
    public $rules = array
    (
        'subject' => array
        (
            'is_required' => array(TRUE, '主题不能为空'),
            'max_length' => array(120, '主题不能超过120个字符'),
        ),
        'content' => array
        (
            'min_length' => array(15, '详细内容不能少于15个字符'),
            'max_length' => array(600, '详细内容不能超过600个字符'),
        ),
    );
    
    public $addrules = array
    (
        'type' => array
        (
            'addrule_valid_type' => '请选择一个有效的类型',
        ),
        'mobile_no' => array
        (
            'addrule_valid_mobile' => '请填写一个有效的手机号码',
        )
    );
    
    //自定义验证器：检查处理类型是否有效
    public function addrule_valid_type($val)
    {
        return isset($this->type_map[$val]);
    }
    
    //自定义验证器：检查手机号码是否有效
    public function addrule_valid_mobile($val)
    {
        return preg_match('/^1[34578]\d{9}$/', $val) != 0;
    }

}
