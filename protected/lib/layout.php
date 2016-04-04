<?php
class layout
{
    public $path;
    
    public function __construct()
    {	
        $this->path = array
        (
            'tpl' => VIEW_DIR.DS.'frontend'.DS.$GLOBALS['cfg']['enabled_theme'].DS.'layout',
            'cache' => APP_DIR.DS.'protected'.DS.'cache'.DS.'template',
            'static' => APP_DIR.DS.'protected'.DS.'cache'.DS.'static',
        );
    }
    
    public function tpl_render($tplname, $assigns = array(), $staticize = FALSE)
    {
        $view = new View($this->path['tpl'], $this->path['cache']);
        if(!empty($assigns)) $view->assign($assigns);
        $contents = $view->render($tplname);
        if($staticize) file_put_contents($this->path['static'].DS.$tplname, $contents);
        return $contents;
    }

    public function check_static_file($filename)
    {
        if($contents = @file_get_contents($this->path['static'].DS.$filename)) return $contents;
        return FALSE;
    }

}
