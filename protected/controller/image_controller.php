<?php
class image_controller extends Controller
{
	public function action_index()
    {
        $i = $_GET['i'];
        $s = $_GET['s'];
        
        if(!empty($i) && !empty($s))
        {
            $im_root = APP_DIR.DS.'upload'.DS.'goods'.DS.'image'.DS;
            $cache_path = $im_root.$s.DS.$i;
            if(is_file($cache_path))
            {
                header('Content-Type:image/jpeg; text/html; charset=utf-8');
                echo @readfile($cache_path);
            }
            else
            {
                $orgi_path = $im_root.$i;
                list($w, $h) = explode('x', $s);
                if(is_file($orgi_path) && self::check_size_valid('goods', $w, $h))
                {
                    $save_path = $im_root.$s.DS.substr($i, 0, strrpos($i, '.'));
                    if(imager::resize($orgi_path, $w, $h, $save_path)) echo @readfile($cache_path);
                }
            }
        }
	}
    
    public function action_album()
    {
        $i = $_GET['i'];
        $s = $_GET['s'];

        if(!empty($i) && !empty($s))
        {
            $im_root = APP_DIR.DS.'upload'.DS.'goods'.DS.'album'.DS;
            $cache_path = $im_root.$s.DS.$i;
            if(is_file($cache_path))
            {
                header('Content-Type:image/jpeg; text/html; charset=utf-8');
                echo @readfile($cache_path);
            }
            else
            {
                $orgi_path = $im_root.$i;
                list($w, $h) = explode('x', $s);
                
                if(is_file($orgi_path) && self::check_size_valid('album', $w, $h))
                {
                    $save_path = $im_root.$s.DS.substr($i, 0, strrpos($i, '.'));
                    if(imager::resize($orgi_path, $w, $h, $save_path)) echo @readfile($cache_path);
                }
            }
        }
    }
    
    public function action_captcha()
    {
        $captcha = new captcha();
        $captcha->create_image();
    }
    
    private static function check_size_valid($type, $w, $h)
    {
        switch($type)
        {
            case 'goods': $limits = $GLOBALS['cfg']['goods_img_thumb']; break;
            case 'album': $limits = $GLOBALS['cfg']['goods_album_thumb']; break;
            default: return FALSE;
        }
        foreach($limits as $v) if($w == $v['w'] && $h == $v['h']) return TRUE;
        return FALSE;
    }


}