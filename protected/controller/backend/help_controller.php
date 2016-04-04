<?php
class help_controller extends general_controller
{
    public function action_index()
    {
        $this->cate_list = $GLOBALS['instance']['cache']->help_cate_model('indexed_list');
        $help_model = new help_model();
        $this->results = $help_model->find_all(null, 'id DESC', 'id, cate_id, title, seq', array(vds_request('page', 1), 15));
        $this->paging = $help_model->page;
        $this->tpl_display('article/help_list.html');
    }

    public function action_add()
    {
        $step = vds_request('step');
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'title' => trim(vds_request('title', '', 'post')),
                'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
                'content' => stripslashes(vds_request('content', '', 'post')),
            );
                
            $help_model = new help_model();
            $verifier = $help_model->verifier($data);
            if(TRUE === $verifier)
            {     
                $help_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加帮助成功', url($this->MOD.'/help', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->cateselect = $GLOBALS['instance']['cache']->help_cate_model('indexed_list');
            $this->tpl_display('article/help.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'title' => trim(vds_request('title', '', 'post')),
                'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
                'content' => stripslashes(vds_request('content', '', 'post')),
            );
            
            $help_model = new help_model();
            $verifier = $help_model->verifier($data);
            if(TRUE === $verifier)
            { 
                if($help_model->update(array('id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新帮助成功', url($this->MOD.'/help', 'index'));
                }  
                else
                {
                    $this->prompt('error', '更新帮助失败');
                }     
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $help_model = new help_model();
            if($this->rs = $help_model->find(array('id' => vds_request('id'))))
            {
                $this->cateselect = $GLOBALS['instance']['cache']->help_cate_model('indexed_list');
                $this->tpl_display('article/help.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_editor()
    {
        $save_path = 'upload'.DS.'article'.DS.'help'.DS;
        $uploader = new uploader($save_path);
        $file = $uploader->upload_file('upfile');
        if($file['error'] == 'success')
        {
            $callback = vds_request('callback');
            $rs = array('state' => 'SUCCESS', 'url' => $file['url']);
            if($callback) echo '<script>'.$callback.'('.json_encode($rs).')</script>';
            echo json_encode($rs);
        }
        else
        {
            echo "<script>alert('{$file['error']}')</script>";
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id');
        if(is_array($id) && !empty($id))
        {
            $affected = 0;
            $help_model = new help_model();
            foreach($id as $v) $affected += $help_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            self::clear_cache();
            $this->prompt('default', "成功删除 {$affected} 个帮助记录, 失败 {$failure} 个", url($this->MOD.'/help', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $GLOBALS['instance']['cache']->help_model('cated_help_list', null, -1);
    }
}