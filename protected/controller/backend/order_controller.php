<?php
class order_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $order_status = vds_request('order_status', '', 'post');
            $order_id = vds_request('order_id', '', 'post');
            
            $where = 'WHERE 1';
            $binds = array();
            if($order_status != '')
            {
                $where .= ' AND order_status = :order_status';
                $binds[':order_status'] = $order_status;
            }
            if($order_id != '')
            {
                $where .= ' AND order_id = :order_id';
                $binds[':order_id'] = $order_id;
            }
            
            $order_model = new order_model();
            $total = $order_model->query("SELECT COUNT(*) as count FROM {$order_model->table_name} {$where}", $binds);  
            if($total[0]['count'] > 0)
            {
                $sort_id = vds_request('sort_id', 0, 'post');
                $sort_map = array('created_date DESC', 'created_date ASC', 'order_amount DESC', 'order_amount ASC');
                $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                
                $order_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
                $limit = $order_model->pager_section();
                
                $sql = "SELECT a.order_id, a.consignee, a.order_status, a.order_amount, a.created_date,
                               b.user_id, b.username
                        FROM {$order_model->table_name} AS a
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user AS b
                        ON a.user_id = b.user_id
                        {$where}
                        ORDER BY {$sort} {$limit}
                       ";
                       
                $list = $order_model->query($sql, $binds);
                $status_map = $order_model->status_map;
                foreach($list as $k => $v)
                {
                    $list[$k]['consignee'] = json_decode($v['consignee'], true);
                    $list[$k]['order_status'] = $status_map[$v['order_status']];
                }
                
                $results = array
                (
                    'status' => 1,
                    'list' => $list,
                    'paging' => $order_model->page,
                );
            }
            else
            {
                $results = array('status' => 0);
            }
            
            echo json_encode($results);
        }
        else
        {
            $order_model = new order_model();
            $this->status_map = $order_model->status_map;
            $this->tpl_display('order/order_list.html');
        }
    }
    
    public function action_view()
    {
        $order_id = vds_request('id', '', 'get');
        $condition = array('order_id' => $order_id);
        $order_model = new order_model();
        if($order = $order_model->find($condition))
        {
            $payment_map = $GLOBALS['instance']['cache']->payment_method_model('indexed_list');
            $shipping_map = $GLOBALS['instance']['cache']->shipping_method_model('indexed_list');
            $order['payment_method_name'] = $payment_map[$order['payment_method']]['name'];
            $order['shipping_method_name'] = $shipping_map[$order['shipping_method']]['name'];
            $order['consignee'] = json_decode($order['consignee'], TRUE);
            $this->order = $order;
            $this->status_map = $order_model->status_map;
            //用户信息
            $user_model = new user_model();
            $this->user = $user_model->find(array('user_id' => $order['user_id']));
            //商品列表
            $order_goods_model = new order_goods_model();
            $goods_list = $order_goods_model->find_all($condition);
            foreach($goods_list as $k => $v) if(!empty($v['goods_opts'])) $goods_list[$k]['goods_opts'] = json_decode($v['goods_opts'], TRUE); 
            $this->goods_list = $goods_list;
            //发货列表
            $shipping_model = new order_shipping_model();
            $this->shipped_list = $shipping_model->find_all($condition, 'dateline DESC');
            $this->carrier_list = $GLOBALS['instance']['cache']->shipping_carrier_model('indexed_list');
            //日志列表
            $log_model = new order_log_model();
            if($this->log_list = $log_model->find_all($condition, 'dateline DESC'))
            {
                $this->admin_list = $GLOBALS['instance']['cache']->admin_model('indexed_list');
                $this->operate_map = $log_model->operate_map;
            }
            
            $this->tpl_display('order/order.html');
        }
        else
        {
            $this->prompt('error', '无法找到相应的订单记录');
        }
    }
    
    public function action_operate()
    {
        $order_id = vds_request('id', '', 'get');
        $condition = array('order_id' => $order_id);
        $order_model = new order_model();
        if($order = $order_model->find(array('order_id' => $order_id)))
        {
            $errno = 0;
            switch (vds_request('step'))
            {
                case 'consignee':
                    
                    if($order['order_status'] == 1 || $order['order_status'] == 2)
                    {
                        $consignee = array
                        (
                            'name' => trim(vds_request('name', '', 'post')),
                            'province' => vds_request('province', '', 'post'),
                            'city' => vds_request('city', '', 'post'),
                            'borough' => vds_request('borough', '', 'post'),
                            'address' => trim(vds_request('address', '', 'post')),
                            'zip' => vds_request('zip', '', 'post'),
                            'mobile_no' => vds_request('mobile_no', '', 'post'),
                            'tel_no' => vds_request('tel_no', '', 'post'),
                        );
                        $consignee_model = new user_consignee_model();
                        $verifier = $consignee_model->verifier($consignee);
                        if(TRUE === $verifier)
                        {
                            $consignee = json_encode($consignee);
                            if($order_model->update(array('order_id' => $order_id), array('consignee' => $consignee)) > 0)
                            {
                                $cause = trim(vds_request('cause', '', 'post'));
                                $log_model = new order_log_model();
                                $log_model->record($order_id, 'consignee', $cause);
                            }
                            else
                            {
                                $errno = 1;
                            }
                        }
                        else
                        {
                             $this->prompt('error', $verifier);
                        }
                    }
                    else
                    {
                        $errno = 2;
                    }
                
                break;
                
                case 'amount': //更改金额
                    
                    if($order['order_status'] == 1)
                    {
                        $order_amount = sprintf('%.2f', abs(vds_request('order_amount', 0, 'post')));
                        if($order_model->update($condition, array('order_amount' => $order_amount)) > 0)
                        {
                            $cause = vds_request('cause', '', 'post');
                            $log_model = new order_log_model();
                            $log_model->record($order_id, 'amount', $cause);
                        }
                        else
                        {
                            $errno = 1;
                        }
                    }
                    else
                    {
                        $errno = 2;
                    }
                
                break;
                
                case 'cancel': //取消交易
                    
                    if($order['order_status'] == 1)
                    {
                        if($order_model->update($condition, array('order_status' => 0)) > 0)
                        {
                            $order_goods_model = new order_goods_model();
                            $order_goods_model->restocking($order_id);
                            $cause = vds_request('cause', '', 'post');
                            $log_model = new order_log_model();
                            $log_model->record($order_id, 'cancel', $cause);
                        }
                        else
                        {
                            $errno = 1;
                        }
                    }
                    else
                    {
                        $errno = 2;
                    }
                    
                break;
                
                case 'resume': //恢复被取消交易
                
                    if($order['order_status'] == 0)
                    {
                        if($order_model->update($condition, array('order_status' => 1)) > 0)
                        {
                            $order_goods_model = new order_goods_model();
                            $order_goods_model->restocking($order_id, 'decr');
                            $cause = trim(vds_request('cause', '', 'post'));
                            $log_model = new order_log_model();
                            $log_model->record($order_id, 'resume', $cause);
                        }
                        else
                        {
                            $errno = 1;
                        }
                    }
                    else
                    {
                        $errno = 2;
                    }
                
                break;
                
                case 'shipping':
                    
                    if($order['order_status'] >= 2 || ($order['order_status'] != 0 && $order['payment_method'] == 2))
                    {
                        $data = array
                        (
                            'order_id' => $order_id,
                            'carrier_id' => vds_request('carrier_id', '', 'post'),
                            'tracking_no' => vds_request('tracking_no', '', 'post'),
                            'memos' => vds_request('memos', '', 'post'),
                            'dateline' => $_SERVER['REQUEST_TIME'],
                        );
                        $shipping_model = new order_shipping_model();
                        if($shipping_model->create($data) > 0)
                        {
                            $order_model->update(array('order_id' => $order_id), array('order_status' => 3));
                        }
                        else
                        {
                            $errno = 1;
                        }
                    }
                    else
                    {
                        $errno = 2;
                    }
                    
                break;
            }
            
            $errormap = array
            (
                0 => '操作成功',
                1 => '操作失败',
                2 => '当前订单无法进行此操作',
            );
            $this->prompt($errno == 0 ? 'success' : 'error', $errormap[$errno], url($this->MOD.'/order', 'view', array('id' => $order_id)));
        }
        else
        {
            $this->prompt('error', '订单不存在');
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        $condition = array('order_id' => $id);
        $order_model = new order_model();
        if($order = $order_model->find($condition))
        {
            if($order['order_status'] == 0)
            {
                if($order_model->delete($condition) > 0)
                {
                    //删除订单商品
                    $order_goods_model = new order_goods_model();
                    $order_goods_model->delete($condition);
                    //删除订单售后
                    $aftersales_model = new aftersales_model();
                    $aftersales_model->delete($condition);
                    
                    $this->prompt('success', '删除成功', url($this->MOD.'/order', 'index'));
                }
                else
                {
                    $this->prompt('error', '删除失败');
                } 
            }
            else
            {
                $this->prompt('error', '该订单无法删除');
            }
        }
        else
        {
            $this->prompt('error', '订单不存在');
        }  
    }
    
}