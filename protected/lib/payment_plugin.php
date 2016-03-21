<?php
abstract class payment_plugin
{
    protected $plugin_parms;
    
    protected $notify_callback;
    
    protected $return_callback;
    
    public function __construct($pcode, $parms = null)
    {
        $this->notify_callback = "{$GLOBALS['cfg']['http_host']}/pay/notify/{$pcode}";
        $this->return_callback = "{$GLOBALS['cfg']['http_host']}/pay/return/{$pcode}";
        $this->plugin_parms = $parms;
    }
    
    abstract protected function get_request_res($order);
    
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
