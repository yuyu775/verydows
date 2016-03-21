<?php
class email_subscription_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $conditions = array();
            $status = vds_request('status', '', 'post');
            $email = vds_request('email', '', 'post');
            if($status != '') $conditions['status'] = $status; 
            if($email != '') $conditions['email'] = $email;
            
            $sort_id = vds_request('sort_id', 0, 'post');
            $sort_map = array('id DESC', 'created_date DESC', 'created_date ASC');
            $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
            
            $email_model = new email_subscription_model();
            if($email_list = $email_model->find_all($conditions, $sort, '*', array(vds_request('page', 1), 15)))
            {
                $results = array
                (
                    'status' => 1,
                    'email_list' => $email_list,
                    'paging' => $email_model->page,
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
            $email_model = new email_subscription_model();
            $this->status_map = $email_model->status_map;
            $this->tpl_display('email/subscription_list.html');
        }
    }
    
    public function action_status()
    {
        $id = vds_request('id');
        if(!empty($id) && is_array($id))
        {
            $status = intval(vds_request('status', 0));
            $affected = 0;
            $email_model = new email_subscription_model();
            foreach($id as $v) $affected += $email_model->update(array('id' => $v), array('status' => $status));
            $failures = count($id) - $affected;
            $handle = $status == 1 ? '确认' : '退订';
            $this->prompt('default', "成功{$handle} {$affected} 个订阅邮箱, 失败 {$failures} 个", url($this->MOD.'/email_subscription', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(is_array($id) && !empty($id))
        {
            $affected = 0;
            $email_model = new email_subscription_model();
            foreach($id as $v) $affected += $email_model->delete(array('id' => $v));
            $failures = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 条订阅邮箱, 失败 {$failures} 条", url($this->MOD.'/email_subscription', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }

}