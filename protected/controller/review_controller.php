<?php
class review_controller extends general_controller
{
    public function action_index()
    {
        $user_id = parent::check_acl();
        $review_model = new goods_review_model();
        $this->review_list = array
        (
            'rows' => $review_model->get_user_reviews($user_id, vds_request('page')),
            'paging' => $review_model->page,
        );
        $this->rating_map = $review_model->rating_map;
        parent::tpl_display('user_review_list.html');
    }
    
    public function action_order()
    {
        $user_id = parent::check_acl();
        if(vds_request('step', null, 'get') == 'submit')
        {
            $order_id = vds_request('order_id', null, 'get');
            $order_model = new order_model();
            if($order = $order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
            {
                if($order['order_status'] == 4)
                {
                    $goods_id = vds_request('goods_id');
                    $order_goods_model = new order_goods_model();
                    if($order_goods_model->find(array('order_id' => $order_id, 'goods_id' => $goods_id)))
                    {
                        $review_model = new goods_review_model();
                        if($review_model->find(array('order_id' => $order_id, 'goods_id' => $goods_id, 'user_id' => $user_id))) parent::prompt('error', '您已对该商品作出过评价');
                        $data = array
                        (
                            'order_id' => $order_id,
                            'goods_id' => $goods_id,
                            'user_id' => $user_id,
                            'rating' => intval(vds_request('rating', 0, 'post')),
                            'content' => strip_tags(vds_request('content', '', 'post')),
                            'created_date' => $_SERVER['REQUEST_TIME'],
                        );           
                                
                        $verifier = $review_model->verifier($data);
                        if(TRUE === $verifier)
                        {
                            $review_model->create($data);
                            parent::prompt('success', '发表商品评价成功');
                        }
                        else
                        {
                            parent::prompt('error', $verifier);
                        }
                    }
                    else
                    {
                        vds_jump(url('main', '404'));
                    }
                }
                else
                {
                    parent::prompt('error', '交易尚未完成，请完成后再评价');
                }
            }
            else
            {
                vds_jump(url('main', '404'));
            }
        }
        else
        {
            $order_id = vds_request('order_id', null, 'get');
            $order_model = new order_model();
            if($order = $order_model->find(array('order_id' => $order_id, 'user_id' => $user_id)))
            {
                if($order['order_status'] == 4)
                {
                    $review_model = new goods_review_model();
                    $order_goods_model = new order_goods_model();
                    $goods_list = $order_goods_model->find_all(array('order_id' => $order_id));
                    foreach($goods_list as $k => $v)
                    {
                        if(!empty($v['goods_opts'])) $goods_list[$k]['goods_opts'] = json_decode($v['goods_opts'], TRUE);
                        $goods_list[$k]['is_reviewed'] = 0;
                        if($review_model->find(array('order_id' => $order_id, 'goods_id' => $v['goods_id']))) $goods_list[$k]['is_reviewed'] = 1;
                    }
                    $this->goods_list = $goods_list;
                    parent::tpl_display('user_order_review.html');
                }
                else
                {
                    parent::prompt('error', '交易尚未完成，您还无法进行此操作');
                }
            }
            else
            {
                vds_jump(url('main', '404'));
            }
        }
    }

}