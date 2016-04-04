<?php
class database_controller extends general_controller
{
    public function action_backup()
    {
        if(vds_request('step', null, 'get') == 'export')
        {
            $tables = vds_request('tables', array(), 'post');
            $tool = new db_tool();
            if(!$tool->export($tables)) echo $tool->error; else echo 'success';
        }
        else
        {
            $model = new Model();
            $sth = $model->db_instance($GLOBALS['mysql'], 'master')->query("SHOW TABLES LIKE '{$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}%'");
            $table_list = $sth->fetchAll(PDO::FETCH_NUM);
            $this->table_list = vds_array_column($table_list, 0);
            $this->tpl_display('tools/database_backup.html');
        }
    }
    
    public function action_restore()
    {
        $backup_dir = APP_DIR.DS.'protected'.DS.'resources'.DS.'backup';
        
        switch(vds_request('step', null, 'get'))
        {
            case 'import':
            
                $file = $backup_dir.DS.vds_request('file', null, 'post');
                $tool = new db_tool();
                if(!$tool->import($file)) echo $tool->error; else echo 'success';
            
            break;
            
            case 'delete':
                
                $file = vds_request('file', null, 'post');
                $error = array();
                
                if(!empty($file))
                {
                    if(is_array($file))
                    {
                        foreach($file as $v)
                        {
                            if(!@unlink($backup_dir.DS.$v)) $error[] = "删除备份文件({$v})失败";
                        }
                    }
                    else
                    {
                        if(!@unlink($backup_dir.DS.$file)) $error[] = "删除备份文件({$file})失败";
                    }
                }
                else
                {
                    $error[] = "必须选择需要删除的备份文件";
                }
                
                if(empty($error))
                {
                    $this->prompt('success', '删除成功', url($this->MOD.'/database', 'restore'));
                }
                else
                {
                    $this->prompt('error', $error);
                }
            
            break;
            
            default:
            
                $file_list = $sort_arr = array();
                if($files = glob($backup_dir.DS.'*.{php,sql,bak}', GLOB_BRACE))
                {
                    foreach($files as $k => $v)
                    {
                        $file_list[$k]['name'] = basename($v);
                        $file_list[$k]['size'] = vds_bytes_to_size(filesize($v));
                        $mtime = filemtime($v);
                        $sort_arr[] = $mtime;
                        $file_list[$k]['mtime'] = $mtime;
                    }
                }
                array_multisort($sort_arr, SORT_DESC, $file_list);
                $this->file_list = $file_list;
                $this->tpl_display('tools/database_restore.html');
        }
    }
    
    public function action_optimize()
    {
        if(vds_request('step', null, 'get') == 'run')
        {
            $tables = vds_request('table', array(), 'post');
            if(!empty($tables))
            {
                $model = new Model();
                foreach($tables as $v) $model->execute("OPTIMIZE TABLE `{$v}`");
                $this->prompt('success', '数据表优化完成', url($this->MOD.'/database', 'optimize'));
            }
            else
            {
                $this->prompt('error', '请选择需要优化的数据表');
            }
        }
        else
        {
            $model = new Model();
            $table_list = array();
            $fragment_totals = 0;
            
            $tables = $model->query("SHOW TABLE STATUS LIKE '{$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}%'");
            foreach($tables as $v)
            {
                if($v['Data_free'] > 0)
                {
                     $table_list[] = array
                     (
                        'table' => $v['Name'],
                        'engine' => $v['Engine'],
                        'rows_count' => $v['Rows'],
                        'data_size' => $v['Data_length'],
                        'index_size' => $v['Index_length'],
                        'fragment_size' => $v['Data_free'],
                     );
                     
                     $fragment_totals += $v['Data_free'];
                }
            }
            
            $this->fragment_totals = vds_bytes_to_size($fragment_totals);
            $this->table_list = $table_list;
            $this->tpl_display('tools/database_optimize.html');
        }
    }
}