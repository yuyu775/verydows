<?php
class pay_controller extends general_controller
{
    public function action_index()
    {
        $user_id = $this->is_logged();
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => vds_request('order_id', null, 'get'), 'user_id' => $user_id)))
        {
            $payment_map = $GLOBALS['instance']['cache']->payment_method_model('indexed_list');
            if($change_payment = vds_request('change_payment', null, 'post'))
            {
                if($change_payment == 2)
                {
                    $order_shipping_model = new order_shipping_model();
                    if($order_shipping_model->find(array('order_id' => $order['order_id']))) $this->prompt('error', '您的订单已发货，无法更改为其他付款方式');
                }
                
                if(isset($payment_map[$change_payment]) && $change_payment != $order['payment_method'])
                {
                    $order_model->update(array('order_id' => $order['order_id']), array('payment_method' => $change_payment));
                    $order['payment_method'] = $change_payment;
                }
            }
            
            if($order['order_status'] == 1)
            {
                $payment = $payment_map[$order['payment_method']];
                $gateway_obj = plugin::instance('payment', $payment['pcode'], array($payment['params']));
                $this->payment_method = array('name' => $payment['name'], 'gateway' => $gateway_obj->create_pay_url($order));
                $this->payment_method_list = $payment_map;
                $this->order = $order;
                $this->tpl_display('pay.html');
            }
            else
            {
                $this->prompt('error', '您无法进行此操作');
            }
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_callback()
    {
        $mode = vds_request('pmode', null, 'get');
        if(in_array($mode, array('notify', 'return')))
        {
            $pcode = vds_request('pcode', '', 'get');
            $payment_model = new payment_method_model();
            if($payment = $payment_model->find(array('pcode' => $pcode, 'enable' => 1), null, 'params'))
            {
                $payment_obj = plugin::instance('payment', $pcode, array($payment['params']));
                if($mode == 'notify')
                {
                    $payment_obj->get_server_res($_POST);
                }
                else
                {
                    $res = $payment_obj->get_return_res($_GET);
                    $this->prompt($res[0], $res[1], $res[2]);
                }
            }
        }
        else
        {
            $this->prompt('error', '参数错误', url('main', 'index'));
        }
    }
}
