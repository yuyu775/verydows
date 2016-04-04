<?php
class order_shipping_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $condition = array();
            $kw = vds_request('kw', '', 'post');
            if($kw != '')
            {
                if(vds_request('type', 0, 'post') == 1) $condition = array('order_id' => $kw);
                else $condition = array('tracking_no' => $kw);
            }
            
            $sort_id = vds_request('sort_id', 0, 'post');
            $sort_map = array('id DESC', 'dateline DESC', 'dateline ASC');
            $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
            
            $shipping_model = new order_shipping_model();
            $list = $shipping_model->find_all($condition, $sort, '*', array(vds_request('page', 1), 15));
            
            if(!empty($list))
            {
                $carrier_list = $GLOBALS['instance']['cache']->shipping_carrier_model('indexed_list');
                foreach($list as $k => $v)
                {
                    $list[$k]['carrier_name'] = $carrier_list[$v['carrier_id']]['name'];
                    $list[$k]['tracking_url'] = $carrier_list[$v['carrier_id']]['tracking_url'] . $v['tracking_no'];
                    $list[$k]['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
                }
                
                $results = array
                (
                    'status' => 1,
                    'shipping_list' => $list,
                    'paging' => $shipping_model->page,
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
            
            $this->tpl_display('order/shipping_list.html');
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(!empty($id) && is_array($id))
        {
            $affected = 0;
            $shipping_model = new order_shipping_model();
            foreach($id as $v) $affected += $shipping_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个发货记录, 失败 {$failure} 个", url($this->MOD.'/order_shipping', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }

}