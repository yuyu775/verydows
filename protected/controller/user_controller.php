<?php
class user_controller extends general_controller
{ 
    public function action_index()
    {   
        $user_id = $this->is_logged();
        $condition = array('user_id' => $user_id);
        $user_model = new user_model();
        $user = $user_model->find($condition);
        $user['last_date'] = $_SESSION['user']['last_date'];
        //用户资料
        $profile_model = new user_profile_model();
        $user['profile'] = $profile_model->find($condition);
        //账户信息
        $account_model = new user_account_model();
        $user['account'] = $account_model->find($condition);
        //用户组信息
        $group_model = new user_group_model();
        $user['group'] = $group_model->get_user_group($user['account']['exp']);
        $group_max_exp = $group_model->query("SELECT MAX(min_exp) AS max FROM {$group_model->table_name}");
        $user['account']['exp_pct'] = round(($user['account']['exp']/$group_max_exp[0]['max'])*100) . '%';
        $this->user = $user;
        //近期订单
        $order_model = new order_model();
        if($order_list = $order_model->find_all($condition, 'created_date DESC', 'order_id, order_amount, order_status, payment_method, created_date', 5))
        {
            foreach($order_list as $k => $v)
            {
                $progress = $order_model->get_user_order_progress($v['order_status'], $v['payment_method']);
                $order_list[$k]['progress'] = array_pop($progress);
            }
        }
        $this->order_list = $order_list;
        //近期收藏
        $favor_model = new user_favorite_model();
        $this->favorite_list = $favor_model->get_user_latest_favorites($user_id, 5);
        //最近浏览
        $goods_model = new goods_model();
        $this->history = $goods_model->get_history();
        
        $this->tpl_display('user_index.html');
    }
    
    public function action_info()
    {
        $res['status'] = 0;
        if($user_id = $this->is_logged(1)) 
        {
             $condition = array('user_id' => $user_id);
             $user_model = new user_model();
             $user = $user_model->find($condition, null, 'user_id, username');
             $profile_model = new user_profile_model();
             $profile = $profile_model->find($condition);
             $info = array
             (
                  'user_id' => $user['user_id'],
                  'username' => $user['username'],
                  'name' => $profile['name'],
                  'avatar' => $profile['avatar'],
             );
             $res = array('status' => 1, 'info' => $info);
        }
        echo json_encode($res);
    }
    
    public function action_profile()
    {
        $user_id = $this->is_logged();
        if(vds_request('step', null, 'get') == 'update')
        {
            $data = array
            (
                'name' => strip_tags(vds_request('name', '', 'post')),
                'gender' => intval(vds_request('gender', 0, 'post')),
                'birth_year' => intval(vds_request('birth_year', 0, 'post')),
                'birth_month' => intval(vds_request('birth_month', 0, 'post')),
                'birth_day' => intval(vds_request('birth_day', 0, 'post')),
                'mobile_no' => trim(vds_request('mobile_no', '', 'post')),
                'qq' => trim(vds_request('qq', '', 'post')),
                'signature' => strip_tags(vds_request('signature', '', 'post')),
            );
            
            $profile_model = new user_profile_model();
            $verifier = $profile_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($profile_model->update(array('user_id' => $user_id), $data) > 0)
                {
                    $this->prompt('success', '更新资料成功', url('user', 'profile'));
                }
                else
                {
                    $this->prompt('error', '更新资料失败');
                }   
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            include(VIEW_DIR.DS.'function'.DS.'html_date_options.php');
            $profile_model = new user_profile_model();
            $this->profile = $profile_model->find(array('user_id' => $user_id));
            $this->tpl_display('user_profile.html');
        }
    }
    
    public function action_avatar()
    {
        $user_id = $this->is_logged(1);
        if(vds_request('step', null, 'get') == 'crop')
        {
            $res['status'] = -1;
            if($user_id)
            {
                $streams = vds_request('streams', '', 'post');
                $mime = vds_request('mime', 'image/jpeg', 'post');
                $res['status'] = 0;
                if($im = imager::create($streams, 'base64'))
                {
                    $region = array
                    (
                        'x' => intval(vds_request('x', 0, 'post')),
                        'y' => intval(vds_request('y', 0, 'post')),
                        'w' => intval(vds_request('w', 0, 'post')),
                        'h' => intval(vds_request('h', 0, 'post')),
                        'tw' => 60,
                        'th' => 60,
                    );
    
                    $save_path = 'upload/user/avatar/'.uniqid($user_id);
                    if($avatar = imager::crop($im, $region, $save_path, $mime))
                    {
                        $res['status'] = 1;
                        $res['avatar'] = substr($avatar, strrpos($avatar, '/') + 1); //返回剪裁后头像图片文件名
                        $profile_model = new user_profile_model();
                        $profile_model->update(array('user_id' => $user_id), array('avatar' => $res['avatar']));
                    }
                }
            }
            echo json_encode($res);
        }
        else //上传
        {
            $res = array('status' => -1, 'data' => '', 'callback' => vds_request('callback_func', 'showCrop', 'post'));
            if($user_id)
            {
                $res['status'] = 0;
                if(!empty($_FILES['avatar_file']['name']))
                {
                    $save_path = 'upload/tmp/'.$user_id;
                    if($tmp = imager::resize($_FILES['avatar_file']['tmp_name'], 300, 300, $save_path))
                    {
                        $res['status'] = 1;
                        $res['data'] = imager::imtobase64($tmp, $_FILES['avatar_file']['type']);
                        @unlink($tmp);
                    }
                }
            }
            echo "<script type=\"text/javascript\">window.parent.{$res['callback']}('{$res['status']}', '{$res['data']}');</script>";
        }
    }
    
    public function action_security()
    {
        switch(vds_request('step', null, 'get'))
        {
            case 'change_email':
            
                $user_id = $this->is_logged();
                $new_email = trim(vds_request('new_email', '', 'post'));
                if(verifier::is_email($new_email, TRUE) && verifier::max_length($new_email, 60))
                {
                    $user_model = new user_model();
                    if($user_model->update(array('user_id' => $user_id), array('email' => $new_email, 'email_status' => 0)) > 0)
                    {
                        $this->prompt('success', '邮箱更改成功', url('user', 'security'));
                    }
                    else
                    {
                        $this->prompt('error', '邮箱更改失败');
                    }
                }
                else
                {
                    $this->prompt('error', '邮箱不符合格式要求');
                }
            
            break;
            
            case 'reset_pwd':
                
                $user_id = $this->is_logged();
                $user_model = new user_model();
                $old_password = trim(vds_request('old_password', '', 'post'));
                if($user_model->find(array('user_id' => $user_id, 'password' => md5($old_password))))
                {
                    $data['password'] = trim(vds_request('new_password', '', 'post'));
                    $data['repassword'] = trim(vds_request('repassword', '', 'post'));
                    $verifier = $user_model->verifier($data, array('username' => FALSE, 'email' => FALSE));
                    if(TRUE === $verifier)
                    {
                        $new_password = md5($data['password']);
                        if($user_model->update(array('user_id' => $user_id), array('password' => $new_password)) > 0)
                        {
                            unset($_SESSION['user']);
                            $this->prompt('success', '密码更改成功', url('user', 'login'));
                        }
                        else
                        {
                            $this->prompt('error', '密码更改失败');
                        }
                    }
                    else
                    {
                        $this->prompt('error', $verifier);
                    }
                }
                else
                {
                    $this->prompt('error', '原密码不正确，请重新输入');
                }
            
            break;
            
            case 'send_validate_mail':
                
                $user_id = $this->is_logged();
                $user_model = new user_model();
                $user = $user_model->find(array('user_id' => $user_id), null, 'username, email, email_status, hash');
                if($user['email_status'] == 0)
                {
                    $tpl_model = new email_tpl_model();
                    if($tpl_model->check_send_count('validate_user_email', $user_id))
                    {
                        $token = base64_encode(vds_encrypt($user['hash'].($_SERVER['REQUEST_TIME'] + 43200).$user['username']));
                        $tpl_vars = array
                        (
                            'username' => $user['username'],
                            'site_name' => $GLOBALS['cfg']['site_name'],
                            'validate_link' => url('user', 'security', array('step' => 'email_validated', 'token' => $token)),
                        );
                        if($tpl_model->sendmail('validate_user_email', $user['email'], $tpl_vars))
                        {
                            $tpl_model->count_send_times('validate_user_email', $user_id);
                            $this->prompt('success', '发送邮件成功, 请登录您的邮箱点击验证链接进行验证', null, 10);
                        }
                        else
                        {
                            $this->prompt('error', '发送邮件失败，请与网站管理员联系!');
                        }
                    }
                    else
                    {
                        $this->prompt('error', '今日发送该邮件次数已超上限');
                    }
                }
                else
                {
                    $this->prompt('error', '您的邮箱已通过验证, 无需再次验证!');
                }
            
            break;
            
            case 'email_validated':
            
                if($token = vds_decrypt(base64_decode(vds_request('token', '', 'get'))))
                {
                    if(intval(substr($token, 40, 10)) > $_SERVER['REQUEST_TIME'])
                    {
                        $user_model = new user_model();
                        if($user = $user_model->find(array('username' => substr($token, 50), 'hash' => substr($token, 0, 40))))
                        {
                            $user_model->update(array('user_id' => $user['user_id']), array('email_status' => 1));
                            $this->prompt('success', '您的邮箱验证通过', 'close', 10);
                        }
                        else
                        {
                            vds_jump(url('main', '404'));
                        }
                    }
                    else
                    {
                        $this->prompt('error', '该验证链接已失效', 'close', 5);
                    }
                }
                else
                {
                    vds_jump(url('main', '404'));
                }
            
            break;
            
            default:
                
                $user_id = $this->is_logged();
                $user_model = new user_model();
                $user = $user_model->find(array('user_id' => $user_id));
                $user['last_date'] = $_SESSION['user']['last_date'];
                $user['last_ip'] = $_SESSION['user']['last_ip'];
                $oauth_model = new user_oauth_model();
                $user['oauth_list'] = $oauth_model->find_all(array('user_id' => $user_id), null, 'party');
                $this->user = $user;
                $this->tpl_display('user_security.html');
        }
    }
	
    public function action_login()
    {
        if(vds_request('step', null, 'get') == 'submit')
        {
            $user_model = new user_model();
            $res = $user_model->login_check(vds_request('username', '', 'post'), vds_request('password', '', 'post'), vds_request('captcha', '', 'post'), vds_request('stay', 0, 'post'));
            if(vds_request('async', 0, 'get') == 0)
            {
                switch($res)
                {
                    case 3: $this->prompt('success', '绑定账号成功', url('user', 'index')); break;
                    case 2: $this->prompt('error', '绑定失败：该授权账户已绑定本站账号', url('user', 'index')); break;
                    case 1: $this->prompt('success', '登录成功', url('user', 'index'), 1); break;
                    case 0: $this->prompt('error', '你输入的验证码不正确, 请重新尝试!', url('user', 'login')); break;
                    case -1: $this->prompt('error', '用户名或密码错误! 请重新尝试！', url('user', 'login')); break;
                }
            }
            else
            {
                echo $res;
            }
        }
        else
        {
            $client_ip = vds_get_ip();
            if($stayed = vds_request('vds_fu_stayed', null, 'cookie'))
            {
                $stayed = vds_decrypt(base64_decode($stayed));
                $user_model = new user_model();
                $actinfo_model = new user_actinfo_model();
                $sql = "SELECT a.user_id, a.hash, b.created_date, b.last_date, b.last_ip
                        FROM {$user_model->table_name} AS a
                        INNER JOIN {$actinfo_model->table_name} AS b
                        ON a.user_id = b.user_id
                        WHERE a.user_id = :user_id
                        LIMIT 1
                        ";
                if($user = $user_model->query($sql, array(':user_id' => substr($stayed, 32))))
                {
                    $user = array_pop($user);
                    if(md5($user['hash'].$user['created_date'].$client_ip) == substr($stayed, 0, 32))
                    {
                        $user_model->set_logined_info($user['user_id'], $user['last_date'], $user['last_ip']);
                        $actinfo_model->update_row($user['user_id'], $_SERVER['REQUEST_TIME'], $client_ip);
                        vds_jump(url('user', 'index'));
                    }
                }
                unset($user);
            }
                
            $security_model = new login_security_model();
            $security_model->check($client_ip);
            $this->salt = $security_model->set_post_salt();
            $this->captcha = $security_model->captcha($GLOBALS['cfg']['captcha_user_login']);
            $oauth_model = new oauth_model();
            $this->oauth_list = $oauth_model->indexed_list();
            $this->tpl_display('login.html');
        }
    }
    
    public function action_register()
    {
        $security_model = new login_security_model();
        if(vds_request('step', null, 'get') == 'submit')
        {
            $captcha = vds_request('captcha', '', 'post');
            if(2 == $security_model->captcha($GLOBALS['cfg']['captcha_user_register'], $captcha)) $this->prompt('error', '验证码不正确', url('user', 'register'));
            
            $data = array
            (
                'username' => trim(vds_request('username', '', 'post')),
                'password' => trim(vds_request('password', '', 'post')),
                'repassword' => trim(vds_request('repassword', '', 'post')),
                'email' => trim(vds_request('email', '', 'post')),
            );
            
            $user_model = new user_model();
            $verifier = $user_model->verifier($data);
            if(TRUE === $verifier)
            {
                $data['password'] = md5($data['password']);
                $data['hash'] = sha1(uniqid(rand(), TRUE));
                unset($data['repassword']);
                if($user_id = $user_model->create($data))
                {
                    $user_model->create_user_info($user_id, vds_request('oauth', null, 'post'));
                    $this->prompt('success', '恭喜您，注册成功！请您务必牢记您的用户名和邮箱.', url('user', 'index'));
                }
                else
                {
                    $this->prompt('error', '注册失败！请稍后重新尝试.');
                }
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->captcha = $security_model->captcha($GLOBALS['cfg']['captcha_user_register']);
            $this->tpl_display('register.html');
        }
    }
    
    public function action_retrieve_pwd()
    {
        switch(vds_request('step'))
        {
            case 'verify':
                
                if(@$_SESSION['captcha'] == strtolower(vds_request('captcha', '', 'post')))
                {
                    $user_model = new user_model();
                    if($user = $user_model->find(array('username' => trim(vds_request('username', '', 'post'))), null, 'username, email'))
                    {
                        $pos = strpos($user['email'], '@');
                        $user['email'] = substr($user['email'], 0, 1) . '*****' . substr($user['email'], $pos - 1, 1) . substr($user['email'], $pos);
                        $this->token = base64_encode(vds_encrypt($_SESSION['captcha'].$user['username']));
                        $this->user = $user;
                        
                        $this->tpl_display('retrieve_password.html');
                    }
                    else
                    {
                        $this->prompt('error', '您输入的用户名不存在，请重试');
                    }
                }
                else
                {
                    $this->prompt('error', '您输入的验证码不正确，请重试');
                }
                
            break;
            
            case 'sendmail':
                
                if($token = vds_decrypt(base64_decode(vds_request('token', null, 'get'))))
                {  
                    $user_model = new user_model();
                    if($user = $user_model->find(array('username' => substr($token, strlen($_SESSION['captcha']))), null, 'username, email, hash'))
                    {
                        $client_ip = vds_get_ip();
                        $tpl_model = new email_tpl_model();
                        if($tpl_model->check_send_count('retrieve_user_password', 0, $client_ip))
                        {
                            $token = base64_encode(vds_encrypt($user['hash'].($_SERVER['REQUEST_TIME'] + 7200).$user['username']));
                            $tpl_vars = array
                            (
                                'username' => $user['username'],
                                'site_name' => $GLOBALS['cfg']['site_name'],
                                'validate_link' => $GLOBALS['cfg']['http_host'].'/index.php?c=user&a=retrieve_pwd&step=validated&token='.$token,
                            );
                        }
                        if($tpl_model->sendmail('retrieve_user_password', $user['email'], $tpl_vars))
                        {
                            $tpl_model->count_send_times('retrieve_user_password', 0, $client_ip);
                            vds_jump(url('user', 'retrieve_pwd', array('step' => 'waiting', 'token' => md5(vds_encrypt($_SESSION['captcha'])))));
                        }
                        else
                        {
                            $this->prompt('error', '发送邮件失败，请与网站管理员联系!');
                        }
                    }
                    
                }
                vds_jump(url('main', '404'));
            
            break;
            
            case 'waiting':
                
                if(isset($_SESSION['captcha']) && md5(vds_encrypt($_SESSION['captcha'])) == vds_request('token', null, 'get'))
                {
                    $this->tpl_display('retrieve_password.html');
                }
                else
                {
                    vds_jump(url('main', '404'));
                }
                
            break;
            
            case 'validated':
                
                if($token = vds_decrypt(base64_decode(vds_request('token', null, 'get'))))
                {
                    if(intval(substr($token, 40, 10)) > $_SERVER['REQUEST_TIME'])
                    {
                        $user_model = new user_model();
                        if($user = $user_model->find(array('username' => substr($token, 50), 'hash' => substr($token, 0, 40))))
                        {
                            $this->token = base64_encode(vds_encrypt($user['username'].$user['password']));
                            $this->tpl_display('retrieve_password.html');
                        }
                        else
                        {
                            vds_jump(url('main', '404'));
                        }
                    }
                    else
                    {
                        $this->prompt('error', '找回密码链接已失效', url('user', 'retrieve_pwd'));
                    }
                }
                else
                {
                    vds_jump(url('main', '404'));
                }
                
            break;
            
            case 'setpwd':
            
                if($token = vds_decrypt(base64_decode(vds_request('token', null, 'get'))))
                {
                    $condition = array('username' => substr($token, 0, strlen($token) - 32), 'password' => substr($token, -32));
                    $user_model = new user_model();
                    if($user_model->find($condition))
                    {
                        $data['password'] = trim(vds_request('password', '', 'post'));
                        $data['repassword'] = trim(vds_request('repassword', '', 'post'));
                        $verifier = $user_model->verifier($data, array('username' => FALSE, 'email' => FALSE));
                        if(TRUE === $verifier)
                        {
                            $data['password'] = md5($data['password']);
                            $user_model->update($condition, array('password' => $data['password'], 'hash' => sha1(vds_random_chars())));
                            $this->tpl_display('retrieve_password.html');
                        }
                        else
                        {
                            $this->prompt('error', $verifier);
                        }
                    }
                    else
                    {
                        vds_jump(url('main', '404'));
                    }
                }
                else
                {
                    vds_jump(url('main', '404'));
                }
            
            break;
            
            default: $this->tpl_display('retrieve_password.html');
        }
    }
    
    public function action_logout()
    {   
        unset($_SESSION['user']);
        unset($_SESSION['oauth']);
        setcookie('vds_fu_stayed', null, $_SERVER['REQUEST_TIME'] - 3600, '/');
        vds_jump(url('user', 'login'));
    }
}