<?php
class admin_model extends Model
{
    public $table_name = 'admin';
    
    public $rules = array
    (
        'repassword' => array
        (
            'equal_to' => array('password', '两次密码不一致'),
        ),
        'email' => array
        (
            'is_email' => array(TRUE, '电子邮箱不符合格式要求'),
            'max_length' => array(60, '电子邮箱不能超过60个字符'),
        ),
        'name' => array
        (
            'is_required' => array(TRUE, '姓名称呼不能为空'),
            'max_length' => array(60, '姓名称呼不能超过60个字符'),
        ),
    );
    
    public $addrules = array
    (
        'username' => array
        (
            'addrule_username_format' => '用户名不符合格式要求',
            'addrule_username_exist' => '用户名已存在',
        ),
        
        'password' => array
        (
            'addrule_password_format' => '密码不符合格式要求',
        ),
    );
    
    //自定义验证器：检查用户名格式(可包含字母、数字或下划线，须以字母开头，长度为5-16个字符)
    public function addrule_username_format($val)
    {
        return preg_match('/^[a-zA-Z][_a-zA-Z0-9]{4,15}$/', $val) != 0;
    }

    //自定义验证器：检查用户名是否存在
    public function addrule_username_exist($val)
    {
        if($this->find(array('username' => $val))) return FALSE;
        return TRUE;
    }
    
    //自定义验证器：检查密码格式(可包含字母、数字或特殊符号，长度为6-32个字符)
    public function addrule_password_format($val)
    {
        return preg_match('/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{5,31}$/', $val) != 0;
    }
    
    /**
     * 设置登录后信息
     */
    public function set_logined_info($admin)
    {
        $active_model = new admin_active_model();
        $active_model->update_active();
        $client_ip = vds_get_ip();
        if($GLOBALS['cfg']['admin_mult_ip_login'] == 0)
        {
            if($active = $active_model->find(array('user_id' => $admin['user_id'])))
            {
                if($active['sess_id'] == vds_request(session_name(), null, 'cookie'))
                {
                     $active_model->delete(array('sess_id' => $active['sess_id']));
                }
                elseif($active['ip'] == $client_ip)
                {
                    $active_model->delete(array('sess_id' => $active['sess_id']));
                }
                else
                {
                     return FALSE;
                }
            }
        }

        $_SESSION['admin'] = array
        (
            'user_id' => $admin['user_id'],
            'username' => $admin['username'],
            'last_ip' => $admin['last_ip'],
            'last_date' => $admin['last_date']
        ); 
        $this->update(array('user_id' => $admin['user_id']), array('last_ip' => $client_ip, 'last_date' => $_SERVER['REQUEST_TIME']));
        $active_model->add_active();
        return TRUE;
    }
    
    /**
     * 管理员列表(以主键作为数据列表索引)
     */
    public function indexed_list()
    {
        $find_all = $this->find_all(null, null, 'user_id, username');
        return vds_array_column($find_all, null, 'user_id');
    }
}