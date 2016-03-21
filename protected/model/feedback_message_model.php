<?php
class feedback_message_model extends Model
{
    public $table_name = 'feedback_message';
    
    public $rules = array
    (
        'content' => array
        (
            'is_required' => array(TRUE, '消息内容不能为空'),
            'max_length' => array(600, '名称不能超过600个字符'),
        ),
    );
}
