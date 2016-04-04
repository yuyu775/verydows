<?php
class favorite_controller extends general_controller
{
    public function action_index()
    {
        $user_id = $this->is_logged();
        $favor_model = new user_favorite_model();
        $this->favorite_list = array
        (
            'rows' => $favor_model->get_user_favorites($user_id, vds_request('page', 1, 'get')),
            'paging' => $favor_model->page,
        );
        $this->tpl_display('user_favorite_list.html');
    }
    
    public function action_add()
    {
        if($user_id = $this->is_logged(1))
        {
            $goods_id = intval(vds_request('id', 0));
            $goods_model = new goods_model();
            if($goods_model->find(array('goods_id' => $goods_id, 'status' => 1)))
            {
                $row = array('user_id' => $user_id, 'goods_id' => $goods_id);
                $favor_model = new user_favorite_model();
                if(!$favor_model->find($row))
                {
                    $row['created_date'] = $_SERVER['REQUEST_TIME'];
                    if($favor_model->create($row)) echo 1; else echo 0;
                }
                else
                {
                    echo -1; //已收藏
                }
            }
            else
            {
                echo -2; //无效的商品参数
            }
        }
        else
        {
            echo -3; //未登录
        }
    }
    
    public function action_delete()
    {
        $user_id = $this->is_logged();
        $id = vds_request('id', null);
        if(!empty($id))
        {
            $favor_model = new user_favorite_model();
            if(is_array($id))
            {
                foreach($id as $v) $favor_model->delete(array('id' => intval($v), 'user_id' => $user_id));
            }
            else
            {
                $favor_model->delete(array('id' => intval($id), 'user_id' => $user_id));
            }
            vds_jump(url('favorite', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误！');
        }
    }
}