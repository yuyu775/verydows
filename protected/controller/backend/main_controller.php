<?php
class main_controller extends general_controller
{
    public function action_index()
    {
        $client_ip = vds_get_ip();
        if($stayed = vds_request('vds_bu_stayed', null, 'cookie'))
        {
            $stayed = vds_decrypt(base64_decode($stayed));
            $admin_model = new admin_model();
            if($admin = $admin_model->find(array('user_id' => substr($stayed, 32))))
            {
                if(md5($admin['hash'].$admin['created_date'].$client_ip) == substr($stayed, 0, 32))
                {
                    if($admin_model->set_logined_info($admin)) vds_jump(url($this->MOD.'/main', 'panel'));
                }
            }
            unset($admin);
        }
        
        $security_model = new login_security_model();
        $lockout = $security_model->check($client_ip);
        if($lockout == 0)
        {
            $this->salt = $security_model->set_post_salt();
            $this->captcha = $security_model->captcha($GLOBALS['cfg']['captcha_admin_login']);
        }
        else
        {
            $this->lockout = $lockout;
        }
        $this->tpl_display('login.html');
    }
    
    public function action_login()
    {
        $client_ip = vds_get_ip();
        $security_model = new login_security_model();
        $lockout = $security_model->check($client_ip);
        if($lockout > 0) $this->prompt('error', "由于您多次输入错误的登录信息，本次登录请求已被拒绝，请于{$lockout}分钟后重新尝试!");
        if($security_model->captcha($GLOBALS['cfg']['captcha_admin_login'], vds_request('captcha', null, 'post')) == 2) $this->prompt('error', '你输入的验证码不正确, 请重新尝试!', url($this->MOD.'/main', 'index'));
        
        $admin_model = new admin_model();
        if($admin = $admin_model->find(array('username' => vds_request('username', '', 'post'))))
        {
            if($security_model->validate_pwd($admin['password'], vds_request('password', '', 'post')))
            {
                if($admin_model->set_logined_info($admin))
                {
                    if(vds_request('stay', 0, 'post') == 1)
                    {
                        $stayed_cookie = md5($admin['hash'].$admin['created_date'].$client_ip).$admin['user_id'];
                        $stayed_cookie = base64_encode(vds_encrypt($stayed_cookie));
                        setcookie('vds_bu_stayed', $stayed_cookie, $_SERVER['REQUEST_TIME'] + 604800, '/');
                    }
                    vds_jump(url($this->MOD.'/main', 'panel'));
                }
                else
                {
                    $logged_time = date('Y-m-d H:i:s', $admin['last_date']);
                    $this->prompt('error', array('该用户已登录系统', "登录IP：{$admin['last_ip']}", "登录时间：{$logged_time}"), null, 5);
                }
            }
        }
        
        $security_model->incr_err($client_ip);
        $this->prompt('error', '错误的用户名或密码, 请重新尝试！', url($this->MOD.'/main', 'index'));
    }
    
    public function action_panel()
    {
        $admin_model = new admin_model();
        $admin = $admin_model->find(array('user_id' => $_SESSION['admin']['user_id']));
        $admin['last_ip'] = $_SESSION['admin']['last_ip'];
        $admin['last_date'] = $_SESSION['admin']['last_date'];
        $this->admin = $admin;
        $this->menus = include(INCL_DIR.DS.'sys_menu.php');
        $this->tpl_display('panel.html');
    }
    
    public function action_dashboard()
    {
        switch(vds_request('step'))
        {
            case 'totals':
            
                $totals = array
                (
                    'order' => self::get_condition_count('order_model'),
                    'revenue' => self::get_condition_count('order_model', 'order_status >= 2', 'SUM(order_amount)'),
                    'user' => self::get_condition_count('user_model'),
                    'goods' => self::get_condition_count('goods_model'),
                    'adv' => self::get_condition_count('adv_model'),
                    'article' => self::get_condition_count('article_model'),
                );
                echo json_encode($totals);
            
            break;
            
            case 'today':
                
                $today_timestamp = strtotime('today');
                $today = array
                (
                    'order' => self::get_condition_count('order_model', "created_date >= {$today_timestamp}"),
                    'revenue' => self::get_condition_count('order_model', "payment_date >= {$today_timestamp}", 'SUM(order_amount)'),
                    'user' => self::get_condition_count('user_actinfo_model', "created_date >= {$today_timestamp}"),
                    'aftersales' => self::get_condition_count('aftersales_model', "created_date >= {$today_timestamp}"),
                    'feedback' => self::get_condition_count('feedback_model', "created_date >= {$today_timestamp}"),
                    'article' => self::get_condition_count('article_model'),
                    'pv' => $GLOBALS['cfg']['visitor_stats'] == 1 ? $today['pv'] = self::get_condition_count('visitor_stats_model', "dateline >= {$today_timestamp}", 'SUM(pv)') : -1,
                );
                echo json_encode($today);
            
            break;
            
            case 'pending':
                
                $today_timestamp = strtotime('today');
                $pending = array
                (
                    'order' => self::get_condition_count('order_model', 'order_status = 2 OR (order_status = 1 AND payment_method = 2)'),
                    'aftersales' => self::get_condition_count('aftersales_model', 'status = 2'),
                    'review' => self::get_condition_count('goods_review_model', 'status = 0'),
                    'feedback' => self::get_condition_count('feedback_model', 'status = 1'),
                    'adv' => self::get_condition_count('adv_model', "end_date != 0 AND end_date <= {$today_timestamp}"),
                    'subscription' => self::get_condition_count('email_subscription_model', 'status = 0'),
                );
                echo json_encode($pending);
            
            break;
            
            case 'sysinfo':
                
                $setting_model = new setting_model();
                $sysinfo = array
                (
                    'vds_version' => "Verydows {$GLOBALS['verydows']['VERSION']} Release {$GLOBALS['verydows']['RELEASE']}",
                    'server_ip' => $_SERVER['SERVER_ADDR'],
                    'server_os' => PHP_OS,
                    'server_soft' => $_SERVER['SERVER_SOFTWARE'],
                    'php_version' => PHP_VERSION,
                    'db_version' => $setting_model->get_db_version(),
                    'db_size' => $setting_model->get_db_size(),
                    'upload_max' => ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'OFF(不允许上传)',
                    'upload_size' => $setting_model->get_upload_size(),
                    'timezone' => date_default_timezone_get(),
                    'visitor_stats' => $GLOBALS['cfg']['visitor_stats'] == 1 ? '开启' : '关闭',
                    'rewrite_enable' => $GLOBALS['cfg']['rewrite_enable'] == 1 ? '开启' : '关闭',
                );
                echo json_encode($sysinfo);
            
            break;
            
            default:
                
                $active_model = new admin_active_model();
                $active_model->update_active();
                $this->admin_list = $active_model->get_active_list();
                $this->version = $GLOBALS['verydows']['VERSION'];
                $this->tpl_display('dashboard.html');
        }
    }
    
    public function action_cleanup()
    {
        $clean = vds_request('clean', null, 'post');
        if($clean == 0)
        {
            $return = self::cleaning_up(1);
            $return = self::cleaning_up(2);
            $return = self::cleaning_up(3);
            $return = self::cleaning_up(4);
        }
        else
        {
            $return = self::cleaning_up($clean);
        }
        
        if($return === FALSE) echo 0; else echo 1;
    }
    
    public function action_reset_password()
    {
        $data['password'] = trim(vds_request('new_password', '', 'post'));
        $data['repassword'] = trim(vds_request('repassword', '', 'post'));
        $admin_model = new admin_model();
        $verifier = $admin_model->verifier($data, array('username' => FALSE, 'email' => FALSE, 'name' => FALSE));
        if(TRUE === $verifier)
        {
            $user_id = $_SESSION['admin']['user_id'];
            $old_password = md5(trim(vds_request('old_password', '', 'post')));
            if($admin_model->find(array('user_id' => $user_id, 'password' => $old_password)))
            {
                $admin_model->update(array('user_id' => $user_id), array('password' => md5($data['password'])));
                $this->prompt('success', '修改密码成功');
            }
            else
            {
                $this->prompt('error', '原密码不正确，请重试');
            }
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_logout()
    {   
        $active_model = new admin_active_model();
        $active_model->delete(array('sess_id' => session_id()));
        unset($_SESSION['admin']);
        setcookie('vds_bu_stayed', null, $_SERVER['REQUEST_TIME'] - 3600, '/');
        vds_jump(url($this->MOD.'/main', 'index'));
    }
    
    public function action_captcha()
    {
        $captcha = new captcha();
        $captcha->create_image();
    }

    /**
     * 获取符合条件的相关数据总数
     */
    private static function get_condition_count($model_name, $condition = null, $col = null)
    {
        $condition = empty($condition) ? '1' : $condition;
        $col = empty($col) ? 'COUNT(*)' : $col;
        $model = new $model_name();
        $sql = "SELECT {$col} AS count FROM {$model->table_name} WHERE {$condition}";
        $rs = $model->query($sql);
        return $rs[0]['count'];
    }
    
    private static function cleaning_up($i)
    {
        switch($i)
        {
            case 1: 
                
                $vcache = new vcache();
                $return = $vcache->clear();
            
            break;
            
            case 2: 
                
                $tempdir = APP_DIR.DS.'protected'.DS.'cache'.DS.'template'.DS;
                foreach(glob($tempdir . '*') as $v) $return = @unlink($v);
            
            break;
            
            case 3:
            
                $tempdir = APP_DIR.DS.'protected'.DS.'cache'.DS.'static'.DS;
                foreach(glob($tempdir . '*') as $v) $return = @unlink($v);
            
            break;
            
            case 4:
            
                $model = new Model();
                $return = $model->execute("DELETE FROM {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}sendmail_limit WHERE dateline < ".strtotime('today'));
                $return = $model->execute("DELETE FROM {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}login_security WHERE expires <= {$_SERVER['REQUEST_TIME']}");
            
            break;
            
            default: return FALSE;
        }
        return $return;
    }
}