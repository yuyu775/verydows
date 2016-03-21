<?php
class shipping_carrier_controller extends general_controller
{
    public function action_index()
    {
        $carrier_model = new shipping_carrier_model();
        $this->results = $carrier_model->find_all(null, 'id DESC');
        $this->tpl_display('shipping/carrier_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'tracking_url' => trim(vds_request('tracking_url', '', 'post')),
                'service_tel' => stripslashes(vds_request('service_tel', '', 'post')),
            );

            $carrier_model = new shipping_carrier_model();
            $verifier = $carrier_model->verifier($data);
            if(TRUE === $verifier)
            {
                $carrier_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加物流承运商成功', url($this->MOD.'/shipping_carrier', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('shipping/carrier.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'tracking_url' => trim(vds_request('tracking_url', '', 'post')),
                'service_tel' => stripslashes(vds_request('service_tel', '', 'post')),
            );
            
            $carrier_model = new shipping_carrier_model();
            $verifier = $carrier_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($carrier_model->update(array('id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新物流承运商成功', url($this->MOD.'/shipping_carrier', 'index'));
                } 
                else
                {
                    $this->prompt('error', '更新物流承运商失败');
                }    
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $carrier_model = new shipping_carrier_model();
            if($this->rs = $carrier_model->find(array('id' => vds_request('id'))))
            {
                $this->tpl_display('shipping'.DS.'carrier.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(!empty($id) && is_array($id))
        {
            $affected = 0;
            $carrier_model = new shipping_carrier_model();
            foreach($id as $v) $affected += $carrier_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            self::clear_cache();
            $this->prompt('default', "成功删除 {$affected} 个记录, 失败 {$failure} 个", url($this->MOD.'/shipping_carrier', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $vcache = new vcache();
        $vcache->shipping_carrier_model('indexed_list', null, -1);
    }
}