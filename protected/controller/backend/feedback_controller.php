<?php
class feedback_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $type = vds_request('type', '', 'post');
            $status = vds_request('status', '', 'post');
            $where = ' WHERE 1';
            $conditions = $binds = array();
            if($type != '')
            {
                $conditions['type'] = $type;
                $where .= ' AND a.type = :type';
                $binds[':type'] = $type;
            }
            if($status != '')
            {
                $conditions['status'] = $status;
                $where .= ' AND a.status = :status';
                $binds[':status'] = $status;
            }
            
            $feedback_model = new feedback_model();
            $total = $feedback_model->find_count($conditions);
            if($total > 0)
            {
                $sort_id = vds_request('sort_id', 0, 'post');
                $sort_map = array('fb_id DESC', 'created_date ASC', 'created_date DESC');
                $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                
                $feedback_model->pager(vds_request('page', 1), 15, 10, $total);
                $limit = $feedback_model->pager_section();
                
                $sql = "SELECT a.fb_id, a.type, a.subject, a.created_date, a.status,
                               b.user_id, b.username
                        FROM {$feedback_model->table_name} AS a
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user AS b
                        ON a.user_id = b.user_id
                        {$where} ORDER BY {$sort} {$limit}
                       ";
                       
                $results = array
                (
                    'status' => 1,
                    'list' => $feedback_model->query($sql, $binds),
                    'paging' => $feedback_model->page,
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
            $feedback_model = new feedback_model();
            $this->type_map = $feedback_model->type_map;
            $this->status_map = $feedback_model->status_map;
            $this->tpl_display('operation/feedback_list.html');
        }
    }
    
    public function action_view()
    {
        $id = vds_request('id', null, 'get');
        $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
        $feedback_model = new feedback_model();
        $sql = "SELECT a.*, b.username
                FROM {$feedback_model->table_name} AS a
                INNER JOIN ".$tblpre."user AS b
                ON a.user_id = b.user_id
                WHERE a.fb_id = :id
                LIMIT 1
               ";
        
        if($row = $feedback_model->query($sql, array(':id' => $id)))
        {
            $this->rs = $row[0];
            $this->type_map = $feedback_model->type_map;
            $this->status_map = $feedback_model->status_map;
            $message_model = new feedback_message_model();
            $this->message_list = $message_model->find_all(array('fb_id' => $id), 'dateline ASC');
            $this->admin_list = $GLOBALS['instance']['cache']->admin_model('indexed_list');
            $this->tpl_display('operation/feedback.html');
        }
        else
        {
            $this->prompt('error', '未找到相应的数据记录');
        }
    }
    
    public function action_reply()
    {
        $data = array
        (
            'fb_id' => vds_request('fb_id'),
            'admin_id' => $_SESSION['admin']['user_id'],
            'content' => strip_tags(trim(vds_request('content', '', 'post'))),
            'dateline' => $_SERVER['REQUEST_TIME'],
        );
        
        $message_model = new feedback_message_model();
        $verifier = $message_model->verifier($data);
        if(TRUE === $verifier)
        {
            $message_model->create($data);
            $this->prompt('success', '回复消息成功');
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_status()
    {
        $fb_id = vds_request('id');
        $status = intval(vds_request('status', 0, 'post'));
        $feedback_model = new feedback_model();
        if($feedback_model->update(array('fb_id' => $fb_id), array('status' => $status)) > 0)
        {
            $this->prompt('success', '操作成功', url($this->MOD.'/feedback', 'view', array('id' => $fb_id)));
        }
        else
        {
            $this->prompt('success', '操作失败');
        }
    }
    
    public function action_delete()
    {
        if(vds_request('step') == 'message')
        {
            $id = vds_request('id');
            if(!empty($id) && is_array($id))
            {
                $message_model = new feedback_message_model();
                $affected = 0;
                foreach($id as $v) if($message_model->delete(array('id' => $v)) > 0) $affected += 1; 
                $failure = count($id) - $affected;
                $this->prompt('default', "成功删除 {$affected} 个消息记录, 失败 {$failure} 个");
            }
            else
            {
                $this->prompt('error', '参数错误');
            }
        }
        else
        {
            $id = vds_request('id');
            $feedback_model = new feedback_model();
            $message_model = new feedback_message_model();
            if(is_array($id))
            {
                $affected = 0;
                foreach($id as $v) 
                {
                    $condition = array('fb_id' => $v);
                    if($feedback_model->delete($condition) > 0)
                    {
                        $affected += 1;
                        $message_model->delete($condition);
                    }
                }
                $failure = count($id) - $affected;
                $this->prompt('default', "成功删除 {$affected} 个咨询反馈记录, 失败 {$failure} 个", url($this->MOD.'/feedback', 'index'));
            }
            else
            {
                $condition = array('fb_id' => $id);
                if($feedback_model->delete($condition) > 0)
                {
                    $message_model->delete($condition);
                    $this->prompt('success', '删除成功', url($this->MOD.'/feedback', 'index'));
                }
                else
                {
                    $this->prompt('error', '删除失败');
                }
            }
        }
    }
}