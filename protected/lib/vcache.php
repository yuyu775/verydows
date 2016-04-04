<?php
class vcache
{
    private $path;
    
    public function __construct($path = null)
    {
        $this->path = $path == null? APP_DIR.DS.'protected'.DS.'cache'.DS.'data'.DS : $path;
    }
    
    public function __call($class, $args)
    {
        if(empty($args[1])) 
        {
            $args[1] = array();
            $key = $class . $args[0];
        }
        else
        {
            $key = $class . $args[0] . serialize($args[1]);
        }
        if(!isset($args[2])) $args[2] = FALSE;
        if($args[2] == -1) return $this->clear($key);
        $cache = $this->get($key, $args[2]);
        if(FALSE == $cache)
        {
            $obj = new $class;
            $data = call_user_func_array(array($obj, $args[0]), $args[1]);
            $this->set($key, $data);
            return $data;
        }
        return $cache;
    }
    
    private function named($key)
    {
        return $this->path . md5($key) . '.php';
    }
    
    public function set($key, $data)
    {
        if($data)
        {
            if(!is_dir($this->path)) mkdir($this->path, 0755);
            $cache_file = $this->named($key);
            $data = '<?php die();?>'.base64_encode(serialize($data));
            return file_put_contents($cache_file, $data);
        }
        return FALSE;
    }
    
    public function get($key, $expire = FALSE)
    {
        $cache_file = $this->named($key);
        if(is_file($cache_file))
        {
            if($expire != FALSE && ($_SERVER['REQUEST_TIME'] - $expire) > filemtime($cache_file))
            {
                $this->clear($key);
                return FALSE;
            }
            
            $data = file_get_contents($cache_file, NULL, NULL, 14);
            return unserialize(base64_decode($data));
        }
        return FALSE;
    }
    
    public function clear($key = NULL)
    {
        if($key != NULL) return @unlink($this->named($key));
        $return = FALSE;
        foreach(glob($this->path . '*') as $cache) $return = @unlink($cache);
        return $return;
    }
}