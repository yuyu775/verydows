<?php
/**
 * COD Payment
 * @author Cigery
 */
class cod extends abstract_payment
{
    public function create_pay_url(&$order)
    {
        return url('pay', 'callback', array('pcode' => 'cod', 'order_id' => $order['order_id']));
    }
    
    public function get_return_res($args)
    {
        if(isset($_SESSION['user']['user_id']))
        {
            $args['user_id'] = $_SESSION['user']['user_id'];
            return $this->get_server_res($args);
        }
        return array('error', '您还未登陆或登录超时', url('user', 'login'));
    }
    
    public function get_server_res($args)
    {
        $order_model = new order_model();
        if($order = $order_model->find(array('user_id' => $args['user_id'], 'order_id' => $args['order_id'])))
        {
            return array('success', '提交订单成功，我们将会尽快为您安排发货', url('order', 'view', array('id' => $order['order_id'])));
        }
        
        return array('error', '付款失败，订单不存在', null);
    }
}