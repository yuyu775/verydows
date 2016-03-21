<?php
class setting_model extends Model
{
    public $table_name = 'setting';
    
    /**
     * 获取全部配置
     */
    public function get_config()
    {
        $find_all = $this->find_all();
        $config = vds_array_column($find_all, 'sv', 'sk');
        $config['rewrite_rule'] = json_decode($config['rewrite_rule'], TRUE);
        $config['goods_img_thumb'] = json_decode($config['goods_img_thumb'], TRUE);
        $config['goods_album_thumb'] = json_decode($config['goods_album_thumb'], TRUE);
        $config['http_host'] = self::get_http_host();
        return $config;
    }
    
    /**
     * 更新配置
     */
    public function update_config()
    {
        $config = $this->get_config();
        $codes = "<?php \nreturn ".var_export($config, TRUE).";";
        return file_put_contents(APP_DIR.DS.'protected'.DS.'setting.php', $codes);
    }
    
    /**
     * 获取数据库版本
     */
    public function get_db_version()
    {
        return $this->statement_sql('SELECT VERSION()')->fetchColumn();
    }
    
    /**
     * 获取数据库大小
     */
    public function get_db_size()
    {   
        $sql = "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size
                FROM information_schema.TABLES WHERE table_schema = '{$GLOBALS['mysql']['MYSQL_DB']}'
                GROUP BY table_schema";
        $size = $this->query($sql);
        return round($size[0]['size'], 2) . ' MB';
    }
    
    /**
     * 获取上传目录大小
     */
    public static function get_upload_size()
    {
        $dir = APP_DIR.DS.'upload';
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) $size += $file->getSize();
        return vds_bytes_to_size($size);
    }
    
    public static function get_http_host()
    {
        $http = self::is_https() === TRUE ? 'https://' : 'http://';
        $host = $http.dirname($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        #if(substr($url, -1) != '/') $url .= '/';
        return $host;
    }
    
    private static function is_https()
    {  
        if(!isset($_SERVER['HTTPS'])) return FALSE;  
        if($_SERVER['HTTPS'] === 1) return TRUE;  
        elseif($_SERVER['HTTPS'] === 'on') return TRUE;
        elseif($_SERVER['SERVER_PORT'] == 443) return TRUE; 
        else return FALSE;
    }
}
