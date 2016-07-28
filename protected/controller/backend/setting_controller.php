<?php
class setting_controller extends general_controller
{
    public function action_index()
    {   
        $setting_model = new setting_model();
        $rs = $setting_model->get_config();
        $rs['themes'] = self::get_available_themes();
        $rs['enabled_theme'] = self::get_enabled_theme();
        $this->rs = $rs;
        $this->tpl_display('setting/index.html');
    }

    public function action_update()
    {
        switch(vds_request('step'))
        {
            case 'global':
            
                $data = array
                (
                    'site_name' => trim(vds_request('site_name', '', 'post')),
                    'encrypt_key' => trim(vds_request('encrypt_key', '', 'post')),
                    'debug' => intval(vds_request('debug', 0, 'post')),
                    'visitor_stats' => intval(vds_request('visitor_stats', 0, 'post')),
                    'admin_mult_ip_login' => intval(vds_request('admin_mult_ip_login', 0, 'post')),
                    'data_cache_lifetime' => intval(vds_request('data_cache_lifetime', 0, 'post')),
                    'footer_info' => stripslashes(vds_request('footer_info', '', 'post')),
                );

            break;
            
            case 'home':
            
                $data = array
                (
                    'home_title' => trim(vds_request('home_title', '', 'post')),
                    'home_keywords' => vds_request('home_keywords', '', 'post'),
                    'home_description' => vds_request('home_description', '', 'post'),
                    'home_newarrival_num' => intval(vds_request('home_newarrival_num', 5, 'post')),
                    'home_recommend_num' => intval(vds_request('home_recommend_num', 5, 'post')),
                    'home_bargain_num' => intval(vds_request('home_bargain_num', 5, 'post')),
                    'home_article_num' => intval(vds_request('home_article_num', 5, 'post')),
                );
                
            break;
       
            case 'goods':
            
                $data = array
                (
                    'goods_hot_searches' => trim(vds_request('goods_hot_searches', '', 'post')),
                    'goods_fulltext_query' => intval(vds_request('goods_fulltext_query', 0, 'post')),
                    'cate_goods_per_num' => intval(vds_request('cate_goods_per_num', 10, 'post')),
                    'goods_search_per_num' => intval(vds_request('goods_search_per_num', 10, 'post')),
                    'goods_history_num' => intval(vds_request('goods_history_num', 5, 'post')),
                    'goods_related_num' => intval(vds_request('goods_related_num', 5, 'post')),
                    'goods_review_per_num' => intval(vds_request('goods_review_per_num', 10, 'post')),
                    'show_goods_stock' => intval(vds_request('show_goods_stock', 0, 'post')),
                    'upload_goods_filetype' => trim(vds_request('upload_goods_filetype', '', 'post')),
                    'upload_goods_filesize' => trim(vds_request('upload_goods_filesize', '', 'post')),
                    'goods_img_thumb' => array(),
                    'goods_album_thumb' => array(),
                ); 
                
                if($thumb_img = vds_request('goods_img_thumb'))
                {
                    if($thumb_img_arr = array_combine($thumb_img['w'], $thumb_img['h']))
                    {
                        $goods_img_thumb = array();
                        foreach($thumb_img_arr as $k => $v) $goods_img_thumb[] = array('w' => intval($k), 'h' => intval($v));
                        $data['goods_img_thumb'] = json_encode($goods_img_thumb);
                    }
                }
                
                if($thumb_album = vds_request('goods_album_thumb'))
                {
                    if($thumb_album_arr = array_combine($thumb_album['w'], $thumb_album['h']))
                    {
                        $goods_album_thumb = array();
                        foreach($thumb_album_arr as $k => $v) $goods_album_thumb[] = array('w' => intval($k), 'h' => intval($v));
                        $data['goods_album_thumb'] = json_encode($goods_album_thumb);
                    }
                }
            
            break;
            
            case 'user':
                
                $data = array
                (
                    'user_register_email_verify' => intval(vds_request('user_register_email_verify', 0, 'post')),
                    'user_review_approve' => intval(vds_request('user_review_approve', 0, 'post')),
                    'upload_avatar_filesize' => trim(vds_request('upload_avatar_filesize', '', 'post')),
                    'user_consignee_limits' => intval(vds_request('user_consignee_limits', 0, 'post')),
                    'order_cancel_expires' => floatval(vds_request('order_cancel_expires', 1, 'post')),
                    'order_delivery_expires' => intval(vds_request('order_delivery_expires', 7, 'post')),
                );
                
            break;
            
            case 'rewrite':
                
                $data['rewrite_enable'] = intval(vds_request('rewrite_enable', 0, 'post'));
                $data['rewrite_rule'] = array();
                $rule = vds_request('rewrite_rule', null, 'post');
                if(is_array($rule) && $rule_arr = array_combine($rule['k'], $rule['v']))
                {
                    foreach($rule_arr as $k => $v)
                    {
                        if(!empty($k) && !empty($v)) $data['rewrite_rule'][trim($k)] = trim($v);
                    }
                    $data['rewrite_rule'] = json_encode($data['rewrite_rule']);
                }
            
            break;
            
            case 'mail':
            
                $data = array
                (
                    'smtp_server' => trim(vds_request('smtp_server', '', 'post')),
                    'smtp_port' => intval(vds_request('smtp_port', 25, 'post')),
                    'smtp_user' => trim(vds_request('smtp_user', '', 'post')),
                    'smtp_password' => trim(vds_request('smtp_password', '', 'post')),
                    'smtp_secure' => vds_request('smtp_secure', '', 'post'),
                );
                
            break;
            
            case 'captcha':
            
                $data = array
                (
                    'captcha_admin_login' => intval(vds_request('captcha_admin_login', 0, 'post')),
                    'captcha_user_login' => intval(vds_request('captcha_user_login', 0, 'post')),
                    'captcha_user_signin' => intval(vds_request('captcha_user_signin', 0, 'post')),
                    'captcha_feedback' => intval(vds_request('captcha_feedback', 0, 'post')),
                );
                
            break;
            
            case 'theme':
                
                $data = array('enabled_theme' => vds_request('theme', '', 'post'));
                
            break;
            
            case 'other':
            
                $data = array
                (
                    'upload_filetype' => trim(vds_request('upload_filetype', '', 'post')),
                    'upload_filesize' => trim(vds_request('upload_filesize', '', 'post')),
                );
            
            break;
        }
        
        $setting_model = new setting_model();
        foreach($data as $k => $v) $setting_model->update(array('sk' => $k), array('sv' => $v));
        if($setting_model->update_config()) $this->prompt('success', '更新设置成功', url($this->MOD.'/setting', 'index'));
        $this->prompt('error', '更新设置失败');
    }
    
    public function action_test_sendmail()
    {
        include(APP_DIR.DS.'plugin'.DS.'phpmailer'.DS.'PHPMailerAutoload.php');
        $mailer = new PHPMailer();
        $smtp_user = trim(vds_request('smtp_user', '', 'post'));
        $mailer->isSMTP();
        $mailer->CharSet = 'UTF-8';
        $mailer->SMTPAuth = TRUE;                 
        $mailer->Host = trim(vds_request('smtp_server', '', 'post'));
        $mailer->Port = trim(vds_request('smtp_port', '', 'post'));
        $mailer->Username = $smtp_user;
        $mailer->Password = trim(vds_request('smtp_password', '', 'post'));
        $mailer->SMTPSecure = vds_request('smtp_secure', '', 'post');
        $mailer->SetFrom($smtp_user, $GLOBALS['cfg']['site_name']);  
        $mailer->addAddress(trim(vds_request('recipient', '', 'post')));
        $mailer->isHTML(FALSE);
        $mailer->Subject = "来自{$GLOBALS['cfg']['site_name']}的测试邮件";
        $mailer->Body = '当您的邮箱收到此封邮件，说明邮件服务器连接正常，可以正常使用邮件发送功能。';
        if(!$mailer->send()) echo $mailer->ErrorInfo; else echo 'success';
    }
    
    //获取当前启用模板主题
    private static function get_enabled_theme()
    {
        $theme = include(VIEW_DIR.DS.'frontend'.DS.$GLOBALS['cfg']['enabled_theme'].DS.'config.php');
        $theme['dirname'] = $GLOBALS['cfg']['enabled_theme'];
        return $theme;
    }
    
    //获取所有可用的模板主题
    private static function get_available_themes()
    {
        $path = VIEW_DIR.DS.'frontend';
        $scanned = array_diff(scandir($path), array('..', '.', 'index.html'));
        $themes = array();
        foreach($scanned as $v)
        {
            $config_file = $path.DS.$v.DS.'config.php';
            if(@is_file($config_file))
            {
                $config = include($config_file);
                $themes[] = $config + array('dirname' => $v);
            }
        }
        return $themes;
    }
}
