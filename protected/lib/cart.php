<?php
class cart
{
    public static function update($step, $key = '', $data = array())
    {
        $cart = self::get_cookie();
        switch($step)
        {
            case 'add':
            
                if(isset($cart['item'][$key])) return FALSE; //已存在购物车中
                $cart['item'][$key] = $data;
                
            break;
            
            case 'remove':
            
                if(!isset($cart['item'][$key])) return FALSE;
                unset($cart['item'][$key]); 
                
            break;
            
            case 'checkout':
            
                if($arr = array_combine($key, $data))
                {
                    foreach($arr as $k => $v) $cart['item'][$k]['qty'] = intval($v) > 0 ? $v : 1;
                }
                else
                {
                    return FALSE;
                }
                
            break;
            
            case 'clear': $cart = null; break;
        }
        self::set_cookie($cart);
        return TRUE;
    }

    public static function get_cart_info()
    {
        $cart = self::get_cookie();
        if(isset($cart['item']))
        {
            $results = array();
            $total_weight = $total_qty = $total_amount = 0;
            $goods_model = new goods_model();
            $opt_model = new goods_optional_model();
            foreach($cart['item'] as $k => $v)
            {
                if($item = $goods_model->find(array('goods_id' => $v['id'], 'status' => 1), null, 'goods_id, goods_name, now_price, goods_image, goods_weight, stock_qty'))
                {
                    if($item['stock_qty'] < $v['qty']) return null;
                    $item['qty'] = $v['qty'];
                    
                    $item['opts'] = array();
                    if($opt_list = $opt_model->find_all(array('goods_id' => $v['id']), null, 'id, type_id, opt_text, opt_price'))
                    {
                        if(!empty($v['opts']) && is_array($v['opts']))
                        {
                            $opt_list_indexed = vds_array_column($opt_list, null, 'id');
                            $vcache = new vcache();
                            $opt_type_map = $vcache->goods_optional_type_model('indexed_list');
                            $item_opts = array();
                            foreach($v['opts'] as $vv)
                            {
                                if(isset($opt_list_indexed[$vv]))
                                {
                                    $item['opts'][$vv]['type'] = $opt_type_map[$opt_list_indexed[$vv]['type_id']];
                                    $item['opts'][$vv]['opt_text'] = $opt_list_indexed[$vv]['opt_text'];
                                    $item['now_price'] = sprintf('%.2f', $item['now_price'] + $opt_list_indexed[$vv]['opt_price']);
                                }
                                else
                                {
                                    return null;
                                }
                            }
                        }
                        else
                        {
                            return null;
                        }
                    }

                    $item['subtotal'] = sprintf('%.2f', $v['qty'] * $item['now_price']);
                    $total_weight += $v['qty'] * $item['goods_weight'];
                    $total_qty += $v['qty'];
                    $total_amount += $item['subtotal'];
                    $results['item'][$k] = $item;
                }
            }
            $results['total_weight'] = $total_weight;
            $results['total_qty'] = $total_qty;
            $results['total_amount'] = sprintf('%.2f', $total_amount);
            return $results;
        }
        
        return null;
    }
    
    public static function get_cookie()
    {
        if(!empty($_COOKIE['vds_cart'])) return json_decode(stripslashes($_COOKIE['vds_cart']), TRUE);
        return array();
    }
    
    public static function set_cookie($cart, $expire = 86400)
    {
        if(!empty($cart['item']))
        {
            $cart = json_encode($cart);
            setcookie('vds_cart', $cart, $_SERVER['REQUEST_TIME'] + $expire, '/');
            $_COOKIE['vds_cart'] = $cart;
        }
        else
        {
            setcookie('vds_cart', null, $_SERVER['REQUEST_TIME'] - $expire, '/');
            $_COOKIE['vds_cart'] = null;
        }
    }
}