<?php
class nav_controller extends general_controller
{
    public function action_index()
    {
        $nav_model = new nav_model();
        $this->pos_map = $nav_model->pos_map;
        $this->results = $nav_model->find_all(null, 'position ASC');
        $this->tpl_display('setting/nav_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'position' => intval(vds_request('position', 0, 'post')),
                'target' => intval(vds_request('target', 0, 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
                'visible' => intval(vds_request('visible', 0, 'post')),
            );
            
            $nav_model = new nav_model();
            $verifier = $nav_model->verifier($data);
            if(TRUE === $verifier)
            {
                $nav_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加导航成功', url($this->MOD.'/nav', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $nav_model = new nav_model();
            $this->pos_map = $nav_model->pos_map;
            $this->tpl_display('setting/nav.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'position' => intval(vds_request('position', 0, 'post')),
                'target' => intval(vds_request('target', 0, 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
                'visible' => intval(vds_request('visible', 0, 'post')),
            );
            
            $nav_model = new nav_model();
            $verifier = $nav_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($nav_model->update(array('id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新导航成功', url($this->MOD.'/nav', 'index'));
                } 
                $this->prompt('error', '更新导航失败', url($this->MOD.'/nav', 'index')); 
            }
            else
            {
                $this->prompt('error', $verifier);
            }
            
        }
        else
        {
            $nav_model = new nav_model();
            if($this->rs = $nav_model->find(array('id' => vds_request('id'))))
            {
                $this->pos_map = $nav_model->pos_map;
                $this->tpl_display('setting/nav.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }

    public function action_delete()
    {
        $id = vds_request('id', array());
        if(is_array($id) && !empty($id))
        {
            $affected = 0;
            $nav_model = new nav_model();
            foreach($id as $v) $affected += $nav_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个记录, 失败 {$failure} 个", url($this->MOD.'/nav', 'index'));
        }
        else
        {
            $this->prompt('error', '无法获取参数');
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $GLOBALS['instance']['cache']->nav_model('get_site_nav', null, -1);
    }
}