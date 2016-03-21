<?php
class order_controller extends general_controller
{
    public function action_cart()
    {
        switch(vds_request('step', null, 'get'))
        {
            case 'bar':
            
               $cart = cart::get_cookie();
               if(!empty($cart['item'])) echo count($cart['item']); else echo 0;
               
            break;
            
            case 'add':
            
                $qty = intval(vds_request('qty', 1, 'post'));
                $data = array
                (
                    'id' => intval(vds_request('id', 0, 'post')),
                    'qty' => $qty > 0 ? $qty : 1,
                    'opts' => vds_request('opts', null, 'post'),
                );
                if(!empty($data['opts']))
                {
                    $key = $data['id'] . '_' . implode('_', $data['opts']);
                }
                else
                {
                    $key = $data['id'];
                }
                if(cart::update('add', $key, $data)) echo 1; else echo 0;
                
            break;
            
            case 'remove':
            
                if(cart::update('remove', vds_request('key', '', 'post'))) echo 1; else echo 0;
                
            break;
            
            case 'clear':
            
                if(cart::update('clear')) echo 1; else echo 0;
                
            break;
            
            case 'checkout':
            
                $key = vds_request('key', array(), 'post');
                $qty = vds_request('qty', array(), 'post');
                if(cart::update('checkout', $key, $qty)) echo 1; else echo 0;
                
            break;
            
            default:
                
                if($id = vds_request('id', null, 'post'))
                {
                    $qty = intval(vds_request('qty', 1, 'post'));
                    $data = array
                    (
                        'id' => intval($id),
                        'qty' => $qty > 0 ? $qty : 1,
                        'opts' => vds_request('opts', null, 'post'),
                    );
                    
                    if(!empty($data['opts']))
                    {
                        $key = $data['id'] . '_' . implode('_', $data['opts']);
                    }
                    else
                    {
                        $key = $data['id'];
                    }
                    cart::update('add', $key, $data);
                }
                
                $this->cart = cart::get_cart_info();
                parent::tpl_display('cart.html');
        }
	}
    
    public function action_confirm()
    {
        $user_id = parent::check_acl();
        
        $cart = cart::get_cart_info();
        if(empty($cart)) vds_jump(url('order', 'cart'));
        
        switch(vds_request('step', null, 'get'))
        {
            case 'shipping':
            
                $method_id = vds_request('shipping_method', 0);
                $consignee_id = vds_request('consignee_id', 0);
                echo self::shipping_calculation($method_id, $consignee_id, $cart);
                
            break;
            
            case 'submit':
            
                $consignee_id = vds_request('consignee_id', 0);
                $shipping_method = vds_request('shipping_method', 0);
                $payment_method = vds_request('payment_method', 0);
                
                $shipping_amount = self::shipping_calculation($shipping_method, $consignee_id, $cart);
                if($shipping_amount >= 0)
                {
                    $payment_method_model = new payment_method_model();
                    if($payment_method_model->find(array('id' => $payment_method)))
                    {
                        $consignee_model = new user_consignee_model();
                        $consignee = $consignee_model->set_order_consignee($consignee_id);
                        
                        $order_model = new order_model();
                        $order_id = $order_model->create_order_id();
                        $data = array
                        (
                            'order_id' => $order_id,
                            'user_id' => $user_id,
                            'consignee' => json_encode($consignee),
                            'shipping_method' => $shipping_method,
                            'payment_method' => $payment_method,
                            'goods_amount' => $cart['total_amount'],
                            'shipping_amount' => $shipping_amount,
                            'order_amount' => $cart['total_amount'] + $shipping_amount,
                            'memos' => strip_tags(vds_request('memos', '')),
                            'created_date' => $_SERVER['REQUEST_TIME'],
                            'order_status' => 1,
                        );
                        
                        $order_model->create($data);
                        $order_goods_model = new order_goods_model();
                        $order_goods_model->add_records($order_id, $cart['item']);
                        cart::update('clear');
                        
                        if($payment_method == 2) parent::prompt('success', '您的订单提交成功，感谢您的购买，我们将会尽快为您安排发货', url('user', 'order', array('step' => 'view', 'id' => $order_id)));
                        
                        vds_jump(url('pay', 'index', array('order_id' => $order_id)));
                    }
                    else
                    {
                        parent::prompt('error', '支付方式不正确,请重新确认');
                    }
                }
                else
                {
                    parent::prompt('error', '无法获取正确的运费数据,请重新确认');
                }
                
            break;
            
            default:
                //用户信息
                $user_model = new user_model();
                $this->user = $user_model->find(array('user_id' => $user_id));
                //收件人信息
                $consignee_model = new user_consignee_model();
                $this->consignee_list = $consignee_model->get_user_consignee_list($user_id);
                //包裹信息
                $this->parcel = $cart;
                //配送和支付方式
                $vcache = new vcache();
                $this->shipping_method_list = $vcache->shipping_method_model('indexed_list');
                $this->payment_method_list = $vcache->payment_method_model('indexed_list');
                
                parent::tpl_display('order_confirm.html');
        }
    }
    
    /**
     * 订单运费计算
     * @param  $method_id       配送方式ID
     * @param  $consignee_id    收件人地址ID
     * @param  $parcel          包裹信息
     * @return 成功返回运费，失败返回对应的错误状态码
     */
    private static function shipping_calculation($method_id, $consignee_id, $parcel)
    {
        $shipping_method_model = new shipping_method_model();
        if($shipping = $shipping_method_model->find(array('id' => $method_id)))
        {
            $consignee_model = new user_consignee_model();
            if($consignee = $consignee_model->find(array('id' => $consignee_id, 'user_id' => $_SESSION['user']['user_id'])))
            {
                $config = json_decode($shipping['params'], TRUE);
                
                foreach($config as $v)
                {
                    if($v['area'] == 0 || in_array($consignee['province'], $v['area']))
                    {
                        switch($v['type']) 
                        {
                            //固定收费
                            case 'fixed': $charges = $v['charges']; break;
                            //计重收费
                            case 'weight': 
                                if($parcel['total_weight'] > $v['first_weight'])
                                {
                                    $charges = $v['first_charges'] + ceil(($parcel['total_weight'] - $v['first_weight']) / $v['added_weight']) * $v['added_charges'];
                                }  
                                else
                                {
                                    $charges = $v['first_charges'];
                                }   
                            break;
                            //计件收费
                            case 'piece': 
                                if($parcel['total_qty'] > $v['first_piece'])
                                {
                                    $charges = $v['first_charges'] + ($parcel['total_qty'] - $v['first_piece']) / $v['added_charges'];
                                }  
                                else
                                {
                                    $charges = $v['first_charges'];
                                }   
                            break;
                            //找不到对应计费方式
                            default: return -4; 
                        }
                        
                        return sprintf('%.2f', $charges);
                    }
                    else
                    {
                        return -3; //该地区无法配送
                    }
                }
            }
            else
            {
                return -2; //未选择收件人地址
            }
        }
        else
        {
            return -1; //未找到对应配送方式
        }
    }
}