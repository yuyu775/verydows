<?php
include(VIEW_DIR.DS.'function'.DS.'backend_paging.php');

class general_controller extends Controller
{
    public function init()
    {
        $this->MOD = substr(strrchr(dirname(__FILE__), DS), 1);
        $acl = new acl($this->MOD);
        $acl->check();
        if(empty($GLOBALS['instance']['cache'])) $GLOBALS['instance']['cache'] = new vcache();
    }
    
    protected function tpl_display($tpl_name)
    {
        $this->display('backend'.DS.$tpl_name);
    }
    
    protected function prompt($type = 'default', $text = '', $redirect = '', $time = 3)
    {
        if(empty($redirect)) $redirect = 'javascript:history.back()';
        $this->rs = array('type' => $type, 'text' => $text, 'redirect' => $redirect, 'time' => $time);
        $this->tpl_display('prompt.html');
        exit;
    }
    
} 