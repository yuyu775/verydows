<?php
class consignee_controller extends general_controller
{
    public function action_index()
    {
        $user_id = $this->is_logged();
        $user_model = new user_model();
        $this->user = $user_model->find(array('user_id' => $user_id));
        $consignee_model = new user_consignee_model();
        $this->consignee_list = $consignee_model->get_user_consignee_list($user_id);
        $consignee_list_count = count($this->consignee_list);
        $this->consignee_num = array
        (
            'total' => $consignee_list_count,
            'remaining' => $GLOBALS['cfg']['user_consignee_limits'] - $consignee_list_count,
        );
        $this->tpl_display('user_consignee_list.html');
    }

    public function action_add()
    {
        $async = vds_request('async', 0);
        if($user_id =$this->is_logged($async))
        {
            $data = array
            (
                'user_id' => $user_id,
                'name' => trim(strip_tags(vds_request('name', ''))),
                'province' => intval(vds_request('province', 0)),
                'city' => intval(vds_request('city', 0)),
                'borough' => intval(vds_request('borough', 0)),
                'address' => trim(strip_tags(vds_request('address', 0))),
                'zip' => trim(strip_tags(vds_request('zip', ''))),
                'mobile_no' => trim(vds_request('mobile_no', '')),
                'tel_no' => trim(strip_tags(vds_request('tel_no', ''))),
            );
            
            $consignee_model = new user_consignee_model();
            $verifier = $consignee_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($consignee_model->find_count(array('user_id' => $user_id)) < $GLOBALS['cfg']['user_consignee_limits'])
                {
                    $data['id'] = $consignee_model->create($data);
                    if($async == 1)
                    {
                         echo json_encode(array('status' => 'success', 'data' => $data));
                    }
                    else
                    {
                        $this->prompt('success', '创建收件人地址成功');
                    }
                }
                else
                {
                    $data = '创建失败，您的收件人地址数量已达到最大限制';
                    if($async == 1)
                    {
                        echo json_encode(array('status' => 'error', 'data' => $data));
                    }
                    else
                    {
                        $this->prompt('error', $data);
                    }
                }
            }
            else
            {
                if($async == 1)
                {
                    echo json_encode(array('status' => 'error', 'data' => $verifier[0]));
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
            }
        }
        else
        {
            echo json_encode(array('status' => 'error', 'data' => '您尚未登录或登录超时'));
        }
    }
    
    public function action_edit()
    {
        $async = vds_request('async', 0);
        if($user_id =$this->is_logged($async))
        {
            $data = array
            (
                'user_id' => $user_id,
                'name' => trim(strip_tags(vds_request('name', ''))),
                'province' => intval(vds_request('province', 0)),
                'city' => intval(vds_request('city', 0)),
                'borough' => intval(vds_request('borough', 0)),
                'address' => trim(strip_tags(vds_request('address', 0))),
                'zip' => trim(strip_tags(vds_request('zip', ''))),
                'mobile_no' => trim(vds_request('mobile_no', '')),
                'tel_no' => trim(strip_tags(vds_request('tel_no', ''))),
            );
            
            $consignee_model = new user_consignee_model();
            $verifier = $consignee_model->verifier($data);
            if(TRUE === $verifier)
            {
                $id = intval(vds_request('id', 0));
                if($consignee_model->update(array('id' => $id, 'user_id' => $user_id), $data) > 0)
                {
                    $data['id'] = $id;
                    if($async == 1)
                    {
                        echo json_encode(array('status' => 'success', 'data' => $data));
                    }
                    else
                    {
                        $this->prompt('success', '更新收件人地址成功');
                    }
                } 
                else
                {
                    if($async == 1)
                    {
                        echo json_encode(array('status' => 'error', 'data' => '更新收件人地址失败'));
                    }
                    else
                    {
                        $this->prompt('error', '更新收件人地址失败');
                    }
                }
            }
            else
            {
                if($async == 1)
                {
                    echo json_encode(array('status' => 'error', 'data' => $verifier[0]));
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
            }
        }
        else
        {
            echo json_encode(array('status' => 'error', 'data' => '您尚未登录或登录超时'));
        }
    }
    
    public function action_as_default()
    {
        $user_id = $this->is_logged();
        $id = intval(vds_request('id', 0, 'get'));
        $consignee_model = new user_consignee_model();
        $consignee_model->update(array('user_id' => $user_id, 'is_default' => 1), array('is_default' => 0));
        $consignee_model->update(array('id' => $id, 'user_id' => $user_id), array('is_default' => 1));
        $this->prompt('success', '设为默认地址成功', url('consignee', 'index'));
    }
    
    public function action_info()
    {
        if($user_id = $this->is_logged(1))
        {
            $res = array();
            $consignee_model = new user_consignee_model();
            if($res['data'] = $consignee_model->find(array('id' => vds_request('id'), 'user_id' => $user_id)))
            {
                $res['status'] = 'success';
                echo json_encode($res);
            }
            else
            {
                echo json_encode(array('status' => 'error', 'data' => '未查询到该收件人信息'));
            }
        }
        else
        {
            echo json_encode(array('status' => 'error', 'data' => '您尚未登录或登录超时'));
        }
    }
    
    public function action_delete()
    {
        $user_id = $this->is_logged();
        $consignee_model = new user_consignee_model();
        if($consignee_model->delete(array('id' => intval(vds_request('id', 0, 'get')), 'user_id' => $user_id)) > 0)
        {
            $this->prompt('success', '删除收件人地址成功', url('consignee', 'index'));
        }  
        else
        {
            $this->prompt('error', '删除收件人地址失败');
        }
    }
}