<?php
class db_tool
{
    public $error = '';
    
    public function export($tables = array(), $save_dir = null, $save_name = null)
    {
        if(empty($save_dir)) $save_dir = APP_DIR.DS.'protected'.DS.'resources'.DS.'backup';
        if(!is_writable($save_dir))
        {
            $this->error = '备份目录不存在或目录无写入权限';
            return FALSE;
        }
        
        $model = new Model();
        
        $db_version = $model->statement_sql('SELECT VERSION()')->fetchColumn();
        $content  = "# -------------------------------------------------------------\n";
        $content .= "# <?php die();?>\n";
        $content .= "# Verydows Database Backup\n";
        $content .= "# Program: Verydows ". VDS_VERSION . " Release " . VDS_RELEASE . "\n";
        $content .= "# MySql: {$db_version} \n";
        $content .= "# Database: {$GLOBALS['mysql']['MYSQL_DB']} \n";
	    $content .= "# Creation: " . date('Y-m-d H:i:s') . "\n";
        $content .= "# Official: http://www.verydows.com\n";
	    $content .= "# -------------------------------------------------------------\n\n";
        
        if(empty($tables))
        {
            $tables = $model->statement_sql("SHOW TABLES LIKE '{$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}%'")->fetchAll(PDO::FETCH_NUM);
            $tables = vds_array_column($tables, 0);
        }
        
        foreach($tables as $table)
        {
            $create = $model->statement_sql("SHOW CREATE TABLE {$table}")->fetch(PDO::FETCH_NUM);
            $content .= "DROP TABLE IF EXISTS `{$table}`;\n{$create[1]};\n\n";
            
            $values = '';
            $rows_query = $model->statement_sql("SELECT * FROM {$table}");
            while($row = $rows_query->fetch(PDO::FETCH_NUM)) $values .= "\n('" . implode("','", array_map('addslashes', $row)) . "'),";
            if($values != '') $content .= "INSERT INTO `{$table}` VALUES" . rtrim($values, ",") . ";\n\n\n";
        }
        
        if(empty($save_name)) $save_name = date('Ymd').'_'.vds_random_chars(10).'.php';
        
        if(file_put_contents($save_dir.DS.$save_name, $content) === FALSE)
        {
            $this->error = '备份失败';
            return FALSE;
        }

        return TRUE;
    }
    
    public function import($file)
    {
        if(file_exists($file))
        {
            $model = new Model();
            
            $streams = str_replace("\r", "\n", file_get_contents($file));
            $line_array = preg_split("/\n/", $streams);
            $sql = '';
            
            foreach($line_array as $line)
            {
                if(preg_match("/^#|^\-\-/", ltrim($line)) && trim($sql) == '') continue;
                
                $sql .= "{$line}\n";
                
                if(!preg_match("/;$/", trim($line))) continue;
                if(substr_count($sql, "/*") != substr_count($sql, "*/")) continue;
                
                $model->execute(trim($sql));
                $sql = '';
            }
            return TRUE;
        }
        
        $this->error = '数据库文件不存在';
        return FALSE;
    }

}
