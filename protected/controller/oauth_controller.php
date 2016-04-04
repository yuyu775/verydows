<?php
class oauth_controller extends general_controller
{
    public function callback()
    {
        $party = vds_request('party', null, 'get');
        if($party)
        {
            $oauth_list = $GLOBALS['instance']['cache']->oauth_model('indexed_list');
            if(isset($oauth_list[$party]))
            {
                $oauth = $oauth_list[$party];
                $oauth_obj = plugin::instance('oauth', $party, array($oauth['params']));
                if($res = $oauth_obj->check_callback($_GET))
                {
                    $oauth_key = $oauth_obj->get_oauth_key();
                    $user_oauth_model = new user_oauth_model();
                    if($user_oauth_model->is_authorized($party, $oauth_key)) $this->prompt('success', '登录成功', url('user', 'index'));
                    $this->party = $oauth['name'];
                    $this->oauth_user = $oauth_obj->get_user_info($res);
                    $security_model = new login_security_model();
                    $this->captcha = array
                    (
                        'register' => $security_model->captcha($GLOBALS['cfg']['captcha_user_register']),
                        'login' => $security_model->captcha($GLOBALS['cfg']['captcha_user_login']),
                    );
                    $this->salt = $security_model->set_post_salt();
                    $this->tpl_display('oauth_bind.html');
                }
                else
                {
                    $this->prompt('error', '授权登陆失败', url('user', 'login'));
                }
            }
            else
            {
                $this->prompt('error', '无效的授权登陆方式', url('user', 'login'));
            }
        }
        else
        {
            $this->prompt('error', '参数非法', url('user', 'login'));
        }
    }
    
    public function action_unbind()
    {
        $user_id = $this->is_logged();
        $oauth_model = new user_oauth_model();
        if($oauth_model->delete(array('user_id' => $user_id, 'party' => vds_request('party', '', 'get'))) > 0)
        {
            $this->prompt('success', '解除绑定成功', url('user', 'security'));
        }
        $this->prompt('error', '解除绑定失败');
    }
}