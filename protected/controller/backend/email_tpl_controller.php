<?php
class email_tpl_controller extends general_controller
{
    public function action_index()
    {
        $tpl_model = new email_tpl_model();
        $this->results = $tpl_model->find_all(null, null, '*', array(vds_request('page', 1), 15));
        $this->paging = $tpl_model->page;
        $this->tpl_display('email/template_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'id' => trim(vds_request('id', '', 'post')),
                'name' => trim(vds_request('name', '', 'post')),
                'subject' => trim(vds_request('subject', '', 'post')),
                'is_html' => intval(vds_request('is_html', 0, 'post')),
                'body' => trim(stripslashes(vds_request('body', '', 'post'))),
            );
            $tpl_model = new email_tpl_model();
            $verifier = $tpl_model->verifier($data);
            if(TRUE === $verifier)
            {
                $tpl_model->save_tpl_file($data['id'], $data['body']);
                unset($data['body']);
                $tpl_model->create($data);
                
                $this->prompt('success', '添加邮件模板成功', url($this->MOD.'/email_tpl', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('email/template.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $orgi_id = vds_request('orgi_id');
            $data = array
            (
                'id' => trim(vds_request('id', '', 'post')),
                'name' => trim(vds_request('name', '', 'post')),
                'subject' => trim(vds_request('subject', '', 'post')),
                'is_html' => intval(vds_request('is_html', 0, 'post')),
                'body' => trim(stripslashes(vds_request('body', '', 'post'))),
            );
            $tpl_model = new email_tpl_model();
            $rule_slices = array();
            if($orgi_id == $data['id']) $rule_slices['id'] = FALSE;
            $verifier = $tpl_model->verifier($data, $rule_slices);
            if(TRUE === $verifier)
            {
                if($orgi_id != $data['id']) @unlink(APP_DIR.DS.'view'.DS.'mail'.DS.$orgi_id.'.html');
                $tpl_model->save_tpl_file($data['id'], $data['body']);
                unset($data['body']);
                $tpl_model->update(array('id' => $orgi_id), $data);
                
                $this->prompt('success', '更新邮件模板成功', url($this->MOD.'/email_tpl', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $tpl_model = new email_tpl_model();
            if($rs = $tpl_model->find(array('id' => vds_request('id'))))
            {
                if(!$rs['body'] = $tpl_model->get_tpl_file($rs['id'])) $rs['body'] = '';
                $this->rs = $rs;
                $this->tpl_display('email/template.html');
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
        $tpl_model = new email_tpl_model();
        if($tpl_model->delete(array('id' => $id)) > 0)
        {
            @unlink(APP_DIR.DS.'view'.DS.'mail'.DS.$id.'.html');
            $this->prompt('success', '成功删除邮件模板', url($this->MOD.'/email_tpl', 'index'));
        }
        else
        {
            $this->prompt('error', '删除邮件模板失败', url($this->MOD.'/email_tpl', 'index'));
        }
    }
    
    
}