<?php
class friendlink_controller extends general_controller
{
    public function action_index()
    {
        $link_model = new friendlink_model();
        $this->results = $link_model->find_all(null, 'id DESC', '*', array(vds_request('page', 1), 15));
        $this->paging = $link_model->page;
        $this->tpl_display('operation/friendlink_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'url' => trim(vds_request('url', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );
            
            $link_model = new friendlink_model();
            $verifier = $link_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($_FILES['logo_file']['name']))
                {
                    $save_path = 'upload'.DS.'friendlink'.DS;
                    $uploader = new uploader($save_path);
                    $logo = $uploader->upload_file('logo_file');
                    if($logo['error'] == 'success') $data['logo'] = $logo['url'];
                    else $this->prompt('error', $logo['error']);
                }
                else
                {
                    $data['logo'] = trim(vds_request('logo_src', '', 'post'));
                }
                
                $link_model->create($data);
                $this->prompt('success', '添加友情链接成功', url($this->MOD.'/friendlink', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('operation/friendlink.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'url' => trim(vds_request('url', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );
            
            $link_model = new friendlink_model();
            $verifier = $link_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($_FILES['logo_file']['name']))
                {
                    $save_path = 'upload'.DS.'friendlink'.DS;
                    $uploader = new uploader($save_path);
                    $logo = $uploader->upload_file('logo_file');
                    if($logo['error'] == 'success') $data['logo'] = $logo['url'];
                    else $this->prompt('error', $logo['error']);
                }
                else
                {
                    $data['logo'] = trim(vds_request('logo_src', '', 'post'));
                }
                
                if($link_model->update(array('id' => vds_request('id')), $data) > 0)
                    $this->prompt('success', '更新友情链接成功', url($this->MOD.'/friendlink', 'index'));
                else
                    $this->prompt('error', '更新友情链接失败');
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $link_model = new friendlink_model();
            if($this->rs = $link_model->find(array('id' => vds_request('id'))))
            {
                $this->tpl_display('operation/friendlink.html');
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
        if(is_array($id) && !empty($id))
        {
            $link_model = new friendlink_model();
            $affected = 0;
            foreach($id as $v) $affected += $link_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个记录, 失败 {$failure} 个", url($this->MOD.'/friendlink', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }
}