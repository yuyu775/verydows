<?php
class payment_method_controller extends general_controller
{
    public function action_index()
    {
        $method_model = new payment_method_model();
        $this->type_map = $method_model->type_map;
        $vcache = new vcache();
        $this->results = $vcache->payment_method_model('indexed_list');
        $this->tpl_display('payment/method_list.html');
    }

    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'instruction' => trim(vds_request('instruction', '', 'post')),
                'params' => vds_request('params', array(), 'post'),
                'seq' => trim(vds_request('seq', 99, 'post')),
                'enable' => intval(vds_request('enable', 0, 'post')),
            );

            $method_model = new payment_method_model();
            $verifier = $method_model->verifier($data);
            if(TRUE === $verifier)
            {
                $data['params'] = json_encode($data['params']);
                if($method_model->update(array('id' => vds_request('id')), $data) > 0)
                {
                    $this->prompt('success', '更新支付方式成功', url($this->MOD.'/payment_method', 'index'));
                }
                else
                {
                    $this->prompt('error', '更新支付方式失败');
                }
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $method_model = new payment_method_model();
            if($rs = $method_model->find(array('id' => vds_request('id'))))
            {
                if(file_exists(VIEW_DIR.DS.'backend'.DS.'payment'.DS.$rs['pcode'].'_config.html'))
                {
                    $rs['config_tpl'] = "backend/payment/{$rs['pcode']}_config.html";
                    $rs['params'] = json_decode($rs['params'], TRUE);
                }
                
                $this->rs = $rs;
                $this->type_map = $method_model->type_map;
                $this->tpl_display('payment/method.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
}