<?php
class balance extends payment_plugin
{
    public function get_request_res($order)
    {
        return url('pay', 'return', array('pcode' => 'balance', 'order_id' => $order['order_id']));
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
        if($order = $order_model->find(array('user_id' => $args['user_id'], 'order_id' => $args['order_id']), null, 'order_amount'))
        {
            $account_model = new user_account_model();
            $account = $account_model->find(array('user_id' => $args['user_id']), null, 'balance');
            if($account['balance'] >= $order['order_amount'])
            {
                $account_model->decr(array('user_id' => $user_id), 'balance', $order['order_amount']);
                $this->save_trade_log($user_id, $order_id, $order['order_amount']);
                
                return array('success', '付款成功', url('user', 'order', array('step' => 'view', 'id' => $args['order_id'])));
            }
            
            return array('error', '您的账户中余额不足，请使用其他支付方式', url('pay', 'index', array('order_id' => $args['order_id'])));
        }
        
        return array('success', '付款成功', null);
    }
    
    private function save_trade_log($user_id, $order_id, $order_amount)
    {
        $log_model = new user_account_log_model();
        $log_data = array
        (
            'user_id' => $user_id,
            'balance' => 0 - $order_amount,
            'cause' => "使用账户余额支付订单[{$order_id}]",
            'created_date' => $_SERVER['REQUEST_TIME'],
        );
        $log_model->create($log_data);
    }
}
?>