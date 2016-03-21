<?php
class order_log_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $kw = vds_request('kw', '', 'post');
            if($kw != '') $condition = array('order_id' => $kw);
            else $condition = null;
            
            $sort_id = vds_request('sort_id', 0, 'post');
            $sort_map = array('id DESC', 'dateline DESC', 'dateline ASC');
            $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
            
            $log_model = new order_log_model();
            $list = $log_model->find_all($condition, $sort, '*', array(vds_request('page', 1), 15));
            
            if(!empty($list))
            {   
                $vcache = new vcache();
                $admin_list = $vcache->admin_model('indexed_list');
                $operate_map = $log_model->operate_map;
                foreach($list as $k => $v)
                {
                    $list[$k]['username'] = $admin_list[$v['admin_id']]['username'];
                    $list[$k]['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
                    $list[$k]['operate'] = $operate_map[$v['operate']];
                }
                
                $results = array
                (
                    'status' => 1,
                    'log_list' => $list,
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
            $this->tpl_display('order/log_list.html');
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(!empty($id) && is_array($id))
        {
            $affected = 0;
            $log_model = new order_log_model();
            foreach($id as $v) $affected += $log_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个日志记录, 失败 {$failure} 个", url($this->MOD.'/order_log', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }

}