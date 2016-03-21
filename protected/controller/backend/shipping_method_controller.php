<?php
class shipping_method_controller extends general_controller
{
    public function action_index()
    {
        $vcache = new vcache();
        $this->results = $vcache->shipping_method_model('indexed_list');
        $this->tpl_display('shipping/method_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'instruction' => trim(vds_request('instruction', '', 'post')),
                'params' => stripslashes(vds_request('params', '', 'post')),
                'seq' => trim(vds_request('seq', 99, 'post')),
                'enable' => intval(vds_request('enable', 0, 'post')),
            );

            $method_model = new shipping_method_model();
            $verifier = $method_model->verifier($data);
            if(TRUE === $verifier)
            {
                $method_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加配送方式成功', url($this->MOD.'/shipping_method', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $area = new area();
            $this->area_select = $area->get_children();
            $this->tpl_display('shipping/method.html');
        }
    }

    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'instruction' => trim(vds_request('instruction', '', 'post')),
                'params' => stripslashes(vds_request('params', '', 'post')),
                'seq' => trim(vds_request('seq', 99, 'post')),
                'enable' => intval(vds_request('enable', 0, 'post')),
            );

            $method_model = new shipping_method_model();
            $verifier = $method_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($method_model->update(array('id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新配送方式成功', url($this->MOD.'/shipping_method', 'index'));
                }    
                else
                {
                    $this->prompt('error', '更新配送方式失败');
                }  
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $method_model = new shipping_method_model();
            if($this->rs = $method_model->find(array('id' => vds_request('id'))))
            {
                $area = new area();
                $this->area_select = $area->get_children();
                $this->tpl_display('shipping/method.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_delete()
    {
        $method_model = new shipping_method_model();
        if($method_model->delete(array('id' => vds_request('id'))) > 0)
        {
            self::clear_cache();
            $this->prompt('success', '删除配送方式成功', url($this->MOD.'/shipping_method', 'index'));
        }    
        else
        {
            $this->prompt('error', '删除配送方式失败');
        }    
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $vcache = new vcache();
        $vcache->shipping_method_model('indexed_list', null, -1);
    }
    
}