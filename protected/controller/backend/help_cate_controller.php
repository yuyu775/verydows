<?php
class help_cate_controller extends general_controller
{
    public function action_index()
    {
        $this->results = $GLOBALS['instance']['cache']->help_cate_model('indexed_list');
        $this->tpl_display('article/help_cate_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'cate_name' => trim(vds_request('cate_name', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
            );

            $cate_model = new help_cate_model();
            $verifier = $cate_model->verifier($data);
            if(TRUE === $verifier)
            {
                $cate_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加帮助分类成功', url($this->MOD.'/help_cate', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('article/help_cate.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'cate_name' => trim(vds_request('cate_name', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
            );
            
            $cate_model = new help_cate_model();
            $verifier = $cate_model->verifier($data);
            if(TRUE === $verifier)
            {
                if($cate_model->update(array('cate_id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新帮助分类成功', url($this->MOD.'/help_cate', 'index'));
                }   
                else
                {
                    $this->prompt('error', '更新帮助分类失败');
                }
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $cate_model = new help_cate_model();
            if($this->rs = $cate_model->find(array('cate_id' => vds_request('id'))))
            {
                $this->tpl_display('article/help_cate.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_delete()
    {
        $cate_model = new help_cate_model();
        if($cate_model->delete(array('cate_id' => vds_request('id'))) > 0)
        {
            self::clear_cache();
            $this->prompt('success', '删除帮助分类成功', url($this->MOD.'/help_cate', 'index'));
        }   
        else
        {
            $this->prompt('error', '删除帮助分类成失败');
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $GLOBALS['instance']['cache']->help_cate_model('indexed_list', null, -1);
        $GLOBALS['instance']['cache']->help_model('cated_help_list', null, -1);
    }
}