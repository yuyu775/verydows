<?php
class order_controller extends general_controller
{
    public function action_index()
    {
        $user_id = $this->is_logged();
        $order_model = new order_model();
        $page_id = vds_request('page', 1, 'get');
        if($order_list = $order_model->find_all(array('user_id' => $user_id), 'created_date DESC', '*', array($page_id, 10)))
        {
            $order_goods_model = new order_goods_model();
            foreach($order_list as $k => $v) $order_list[$k]['goods_list'] = $order_goods_model->get_goods_list($v['order_id']);
        }
                
        $this->order_list = array('rows' => $order_list, 'paging' => $order_model->page);
        $this->payment_map = $GLOBALS['instance']['cache']->payment_method_model('indexed_list');
        $this->tpl_display('user_order_list.html');
    }
    
    public function action_view()
    {
        $user_id = $this->is_logged();
        $order_id = vds_request('id', '', 'get');
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
        {
            $condition = array('order_id' => $order_id);
            $order['consignee'] = json_decode($order['consignee'], TRUE);
            $order_goods_model = new order_goods_model();
            $this->goods_list = $order_goods_model->get_goods_list($order_id);
            $this->progress = $order_model->get_user_order_progress($order['order_status'], $order['payment_method']);
            $this->status_map = $order_model->status_map;
            $this->payment_method_list = $GLOBALS['instance']['cache']->payment_method_model('indexed_list');
            $this->shipping_method_list = $GLOBALS['instance']['cache']->shipping_method_model('indexed_list');
            if($order['order_status'] == 3)
            {
                $this->carrier_list = $GLOBALS['instance']['cache']->shipping_carrier_model('indexed_list');
                $shipping_model = new order_shipping_model();
                if($shipping = $shipping_model->find($condition, 'dateline DESC'))
                {
                    $this->shipping = $shipping;
                    $shipping['countdown'] = $shipping['dateline'] + $GLOBALS['cfg']['order_delivery_expires'] * 86400 - $_SERVER['REQUEST_TIME'];
                    if($shipping['countdown'] <= 0) $order_model->update($condition, array('order_status' => 4));
                    $this->shipping = $shipping;
                }
            }
            $this->order = $order;
            $this->tpl_display('user_order_details.html');
        }
        else
        {
            $this->prompt('error', '订单不存在');
        }
    }
    
    public function action_cancel()
    {
        $user_id = $this->is_logged();
        $order_id = vds_request('id', '', 'get');
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
        {
            if($order['order_status'] == 1)
            {
                $order_model->update(array('order_id' => $order_id), array('order_status' => 0));
                $order_goods_model = new order_goods_model();
                $order_goods_model->restocking($order_id);
                $this->prompt('success', '取消订单成功', url('order', 'view', array('id' => $order_id)));
            }
            else
            {
                $this->prompt('error', '参数非法');
            }
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_delivered()
    {
        $user_id = $this->is_logged();
        $order_id = vds_request('id', '', 'get');
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
        {
            if($order['order_status'] == 3)
            {
                $order_model->update(array('order_id' => $order_id), array('order_status' => 4));
                $this->prompt('success', '签收成功，感谢您的购买！如有任何售后问题请及时与客服联系', url('order', 'view', array('id' => $order_id)), 5);
            }
            else
            {
                $this->prompt('error', '参数非法');
            }
        }
        
        vds_jump(url('main', '404'));
    }
    
    public function action_rebuy()
    {
        $user_id = $this->is_logged();
        $order_id = vds_request('id', '', 'get');
        $order_model = new order_model();
        if($order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
        {
            $order_goods_model = new order_goods_model();
            $goods_list = $order_goods_model->find_all(array('order_id' => $order_id), null, 'goods_id, goods_opts, goods_qty');
            foreach($goods_list as $v)
            {
                $opt_key = '';
                $opt_ids = null;
                if(!empty($v['goods_opts']))
                {
                    $opts = json_decode($v['goods_opts'], TRUE);
                    foreach($opts as $kk => $vv)
                    {
                        $opt_key = '_'.$kk;
                        $opt_ids[] = $kk;
                    }
                }
                cart::update('add', $v['goods_id'].$opt_key, array('id' => $v['goods_id'], 'qty' => $v['goods_qty'], 'opts' => $opt_ids));
            }
            vds_jump(url('cart', 'index'));
        }
        
        vds_jump(url('main', '404'));
    }
}