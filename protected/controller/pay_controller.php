<?php
class pay_controller extends general_controller
{
    public function action_index()
    {
        $user_id = parent::check_acl();
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => vds_request('order_id', null, 'get'), 'user_id' => $user_id)))
        {
            $vcache = new vcache();
            $payment_method_list = $vcache->payment_method_model('indexed_list');

            if($change_payment = vds_request('change_payment', null, 'post'))
            {
                if($change_payment == 2)
                {
                    $order_shipping_model = new order_shipping_model();
                    if($order_shipping_model->find(array('order_id' => $order['order_id']))) parent::prompt('error', '您的订单已发货，无法更改为其他付款方式');
                }
                
                if(isset($payment_method_list[$change_payment]) && $change_payment != $order['payment_method'])
                {
                    $order_model->update(array('order_id' => $order['order_id']), array('payment_method' => $change_payment));
                    $order['payment_method'] = $change_payment;
                }
            }
            
            if($order['order_status'] == 1)
            {
                if($order['payment_method'] == 2) parent::prompt('success', '提交订单成功，我们将会尽快为您安排发货', url('user', 'order', array('step' => 'view', 'id' => $order['order_id'])));
                
                $vcache = new vcache();
                $payment_method_list = $vcache->payment_method_model('indexed_list');
                $payment_method = $payment_method_list[$order['payment_method']];
                include(APP_DIR.DS.'plugin'.DS.'payment'.DS.$payment_method['pcode'].'.php');
                $gateway_obj = new $payment_method['pcode']($payment_method['pcode'], $payment_method['params']);
                $this->payment_method = array('name' => $payment_method['name'], 'gateway' => $gateway_obj->get_request_res($order));
                $this->payment_method_list = $payment_method_list;
                $this->order = $order;
                parent::tpl_display('pay.html');
            }
            else
            {
                parent::prompt('error', '您无法进行此操作');
            }
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_notify()
    {
        $pcode = vds_request('pcode', '', 'get');
        $method_model = new payment_method_model();
        if($method = $method_model->find(array('pcode' => $pcode)))
        {
            include(APP_DIR.DS.'plugin'.DS.'payment'.DS.$method['pcode'].'.php');
            $payment_obj = new $pcode($pcode, $method['params']);
            $payment_obj->get_server_res($_POST);
        }
    }
    
    public function action_return()
    {
        $pcode = vds_request('pcode', null, 'get');
        $method_model = new payment_method_model();
        if($method = $method_model->find(array('pcode' => $pcode)))
        {
            include(APP_DIR.DS.'plugin'.DS.'payment'.DS.$pcode.'.php');
            $payment_obj = new $pcode($pcode, $method['params']);
            $res = $payment_obj->get_return_res($_GET);
            parent::prompt($res[0], $res[1], $res[2]);
        }
        else
        {
            parent::prompt('error', '未找到对应的付款方式');
        }
    }
}