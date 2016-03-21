<?php
include(VIEW_DIR.DS.'function'.DS.'layout.php');
include(VIEW_DIR.DS.'function'.DS.'reviser.php');

class general_controller extends Controller
{
    public function init()
    {
        self::auto_task();
        $this->common = array
        (
            'site_name' => $GLOBALS['cfg']['site_name'],
            'url' => $GLOBALS['cfg']['http_host'],
            'theme' => $GLOBALS['cfg']['http_host'].'/public/theme/frontend/'.$GLOBALS['cfg']['enabled_theme'],
        );
    }

    protected function tpl_display($tpl_name)
    {
        parent::display('frontend'.DS.$GLOBALS['cfg']['enabled_theme'].DS.$tpl_name);
    }
    
    protected function prompt($type = null, $text = '', $redirect = null, $time = 3)
    {
        if(empty($type)) $type = 'default';
        if(empty($redirect)) $redirect = 'javascript:history.back()';
        $this->rs = array('type' => $type, 'text' => $text, 'redirect' => $redirect, 'time' => $time);
        $this->tpl_display('prompt.html');
        exit;
    }
    
    protected function check_acl($async = 0)
    {
        if(empty($_SESSION['user']['user_id']))
        {
            if($async == 1) return FALSE;
            vds_jump(url('user', 'login'));
        }
        return $_SESSION['user']['user_id'];
    }
    
    //每隔一小时执行定时任务
    private static function auto_task()
    {
        $timer = APP_DIR.DS.'protected'.DS.'resources'.DS.'timer.txt';
        if($_SERVER['REQUEST_TIME'] - file_get_contents($timer) >= 3600)
        {
            $order_model = new order_model();
            $order_model->auto_cancel();
            file_put_contents($timer, $_SERVER['REQUEST_TIME']);
        }
    }
} 