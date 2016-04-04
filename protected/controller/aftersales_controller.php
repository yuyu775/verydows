<?php
class aftersales_controller extends general_controller
{
    public function action_index()
    {
        $user_id = $this->is_logged();
        $aftersales_model = new aftersales_model();
        if($rows = $aftersales_model->find_all(array('user_id' => $user_id), 'as_id DESC', 'as_id, order_id, type, goods_id, goods_qty, created_date', array(vds_request('page', 1), 10)))
        {
            $type_map = $aftersales_model->type_map;
            $order_goods_model = new order_goods_model();
            foreach($rows as $k => $v)
            {
                $rows[$k]['type'] = $type_map[$v['type']];
                $goods = $order_goods_model->find(array('order_id' => $v['order_id'], 'goods_id' => $v['goods_id']), null, 'goods_name, goods_image, goods_opts');
                if(!empty($goods['goods_opts'])) $goods['goods_opts'] = json_decode($goods['goods_opts'], TRUE);
                $rows[$k]['goods'] = $goods;
                unset($goods);
            }
            unset($type_map);
        }
                
        $this->aftersales_list = array
        (
            'rows' => $rows,
            'paging' => $aftersales_model->page,
        );
        $this->tpl_display('user_aftersales_list.html');
    }
    
    public function action_view()
    {
        $user_id = $this->is_logged();
        $as_id = vds_request('id', null, 'get');
        $aftersales_model = new aftersales_model();
        if($aftersales = $aftersales_model->find(array('as_id' => $as_id, 'user_id' => $user_id)))
        {
            $type_map = $aftersales_model->type_map;
            $aftersales['type'] = $type_map[$aftersales['type']];
            $this->aftersales = $aftersales;
            $message_model = new aftersales_message_model();
            $this->message_list = $message_model->find_all(array('as_id' => $as_id), 'dateline ASC');
            $this->tpl_display('user_aftersales_details.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_order()
    {
        $user_id = $this->is_logged();
        $order_id = vds_request('order_id', null, 'get');
        $order_model = new order_model();
        if($order_model->find(array('user_id' => $user_id, 'order_id' => $order_id, 'order_status' => 4)))
        {
            $order_goods_model = new order_goods_model();
            $this->goods_list = $order_goods_model->get_goods_list($order_id);
            $this->tpl_display('user_aftersales_order.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_apply()
    {
        $user_id = $this->is_logged();
        if(vds_request('step', null, 'get') == 'submit')
        {
            $order_id = vds_request('order_id', null, 'post');
            $goods_id = vds_request('goods_id', null, 'post');
            $goods_qty = vds_request('goods_qty', null, 'post');
            $aftersales_model = new aftersales_model();
            if($aftersales_model->check_apply_valid($user_id, $order_id, $goods_id, $goods_qty))
            {
                $data = array
                (
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'goods_id' => $goods_id,
                    'goods_qty' => $goods_qty,
                    'type' => vds_request('type', null, 'post'),
                    'cause' => strip_tags(vds_request('cause', '', 'post')),
                    'mobile_no' => trim(vds_request('mobile_no', '', 'post')),
                    'created_date' => $_SERVER['REQUEST_TIME'],
                    'status' => 1,
                );
                    
                $verifier = $aftersales_model->verifier($data);
                if(TRUE === $verifier)
                {
                    $aftersales_model->create($data);
                    $this->prompt('success', '提交申请成功', url('aftersales', 'index'));
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
            }
            else
            {
                $this->prompt('error', '不符合申请售后要求');
            }
        }
        else
        {
            $order_id = vds_request('order_id', null, 'get');
            $goods_id = vds_request('goods_id', null, 'get');
            $aftersales_model = new aftersales_model();
            if($aftersales_model->check_apply_valid($user_id, $order_id, $goods_id))
            {
                $order_goods_model = new order_goods_model();
                $goods = $order_goods_model->find(array('order_id' => $order_id, 'goods_id' => $goods_id));
                $goods['goods_opts'] = !empty($goods['goods_opts']) ? json_decode($goods['goods_opts'], TRUE) : array();
                $this->goods = $goods;
                $this->tpl_display('user_aftersales_apply.html');
            }
            else
            {
                vds_jump(url('main', '404'));
            }
        }
    }
    
    public function action_messaging()
    {
        $user_id = $this->is_logged();
        $as_id = intval(vds_request('id', null, 'get'));
        $aftersales_model = new aftersales_model();
        if($aftersales_model->find(array('as_id' => $as_id, 'user_id' => $user_id, 'status' => 1)))
        {
            $data = array
            (
                'as_id' => $as_id,
                'content' => strip_tags(vds_request('content', '', 'post')),
                'dateline' => $_SERVER['REQUEST_TIME'],
            );

            $message_model = new aftersales_message_model();
            $verifier = $message_model->verifier($data);
            if(TRUE === $verifier)
            {
                $message_model->create($data);
                $this->prompt('success', '发送消息成功', url('aftersales', 'view', array('id' => $as_id)));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
}