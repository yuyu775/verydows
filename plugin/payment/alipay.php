<?php
class alipay extends payment_plugin
{
    private function set_config()
    {
        $config = json_decode($this->plugin_parms, TRUE);
        return array
        (
            'partner'        => $config['partner'], //合作身份者id，以2088开头的16位纯数字
            'seller_email'   => $config['seller_email'], //收款支付宝账号
            'key'            => $config['key'], //安全检验码，以数字和字母组成的32位字符
            'service'        => 'trade_create_by_buyer',
        );
    }
    
    public function get_request_res($order)
    {
        $config = $this->set_config();
        //请求参数
        $params = array
        (
            'service'        => 'trade_create_by_buyer',
            'partner'        => $config['partner'],
            'seller_email'   => $config['seller_email'],
            'payment_type'   => '1', //支付类型
            'notify_url'     => $this->notify_callback,
            'return_url'     => $this->return_callback,
            'out_trade_no'   => $order['order_id'],
            'subject'        => "{$GLOBALS['cfg']['site_name']}的付款订单[{$order['order_id']}]",
            'total_fee'      => $order['order_amount'],
            'show_url'       => $GLOBALS['cfg']['http_host'],
            '_input_charset' => 'utf-8',
            'transport'      => 'http',
        );

        return $this->create_pay_url($params);
    }
    
    public function get_server_res($args)
    {
        if($this->check_sign($args))
        {
            if($args['trade_status'] == 'TRADE_FINISHED' || $args['trade_status'] == 'TRADE_SUCCESS')
            {
               $this->save_trade_res($args['out_trade_no'], $args['trade_no']);
               return TRUE;
            }
        }
        return FALSE;
    }
    
    public function get_return_res($args)
    {
        if($this->check_sign($args))
        {
            $prompt = array('success', '付款成功', url('user', 'order', array('step' => 'view', 'id' => $args['out_trade_no'])));
        }
        else
        {
            $prompt = array('error', '付款失败', null);
        }
        return $prompt;
    }
    
    private function create_pay_url($params)
    {
        $gateway = 'https://mapi.alipay.com/gateway.do?';
        ksort($params);
        $args = $sign = '';
        
        foreach($params as $k => $v)
        {
            $args .= $k.'='.urlencode($v).'&';
            $sign .= $k.'='.$v.'&';
        }
        $args = substr($args, 0, strlen($args) - 1);
        
        $config = $this->set_config();
        $sign = md5(substr($sign, 0, strlen($sign) - 1) . $config['key']);
        
        return $gateway . $args . '&sign='. $sign . '&sign_type=MD5';
    }
    
    private function check_sign($args)
    {
        if(empty($args) || empty($args['sign'])) return FALSE;
        
        $sign = $args['sign'];
        ksort($args);
        
        $args_str = '';
        foreach($args as $k => $v)
        {
            if($k == 'sign' || $k == 'sign_type' || $v == '') continue;
            $args_str .= $k.'='.$v.'&';
        }
        if(get_magic_quotes_gpc()) $args_str = stripslashes($args_str);
        
        $config = $this->set_config();
        $args_str = substr($args_str, 0, strlen($args_str) - 1) . $config['key'];

        if($sign == md5($args_str)) return TRUE;
        return FALSE;
    }

}
?>