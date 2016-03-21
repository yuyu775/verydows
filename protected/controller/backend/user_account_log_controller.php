<?php
class user_account_log_controller extends general_controller
{
	public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $where = ' WHERE 1';
            $condition = array();
            $username = trim(vds_request('username', '', 'post'));
            if($username != '')
            {
                $user_model = new user_model();
                if($user = $user_model->find(array('username' => $username), null, 'user_id'))
                {
                    $condition = array('user_id' => $user['user_id']);
                    $where .=  " AND a.user_id = {$user['user_id']}";
                }
                else
                {
                    $condition = array('user_id' => -1);
                }                
            }
            
            $log_model = new user_account_log_model();
            $total = $log_model->find_count($condition);
            if($total > 0)
            {
                $sort_id = vds_request('sort_id', 0, 'post');
                $sort_map = array('id DESC', 'dateline DESC', 'dateline ASC');
                $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                
                $log_model->pager(vds_request('page', 1), 15, 10, $total);
                $limit = $log_model->pager_section();
            
                $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
                $sql = "SELECT a.*, b.username AS user, c.username AS admin
                        FROM {$log_model->table_name} AS a
                        LEFT JOIN {$tblpre}user AS b
                        ON a.user_id = b.user_id
                        LEFT JOIN {$tblpre}admin AS c
                        ON a.admin_id = c.user_id
                        {$where} ORDER BY {$sort} {$limit}
                       ";
                
                $results = array
                (
                    'status' => 1,
                    'log_list' => $log_model->query($sql),
                    'paging' => $log_model->page,
                );
            }
            else
            {
                $results = array('status' => 0);
            }
            echo json_encode($results);
        }
        else
        {
            $this->tpl_display('user/account_log_list.html');
        }
	}
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(!empty($id) && is_array($id))
        {
            $affected = 0;
            $log_model = new user_account_log_model();
            foreach($id as $v)
            {
                $affected += $log_model->delete(array('id' => $v));
            }
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 条日志记录, 失败 {$failure} 条", url($this->MOD.'/user_account_log', 'index'));
        }
        else
        {
            $this->prompt('error', '无法获取参数');
        }
    }
    
}