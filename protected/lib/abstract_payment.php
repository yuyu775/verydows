<?php
abstract class abstract_payment
{
    protected $config = array();
    
    protected $notify_callback;
    
    protected $return_callback;
    
    public function __construct($params = null)
    {
        $pcode = get_class($this);
        $this->notify_callback = "{$GLOBALS['cfg']['http_host']}/pay/callback/notify/{$pcode}";
        $this->return_callback = "{$GLOBALS['cfg']['http_host']}/pay/callback/return/{$pcode}";
        if(!empty($params)) $this->config = json_decode($params, TRUE);
    }
    
    abstract protected function create_pay_url(&$order);
    
    abstract protected function get_server_res($args);
    
    abstract protected function get_return_res($args);

    protected function save_trade_res($order_id, $trade_no)
    {
        $data = array
        (
            'order_status' => 2,
            'trade_no' => $trade_no,
            'payment_date' => $_SERVER['REQUEST_TIME'],
        );
        $order_model = new order_model();
        return $order_model->update(array('order_id' => $order_id), $data);
    }
}