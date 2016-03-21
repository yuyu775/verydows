<?php
class user_group_controller extends general_controller
{
    public function action_index()
    {
        $group_model = new user_group_model();
        $groups = $group_model->find_all(array(), 'min_exp ASC');
        $n = count($groups) - 1;
        foreach($groups as $k => $v)
        {
            if($k < $n) $v['max_exp'] = $groups[$k + 1]['min_exp']; else $v['max_exp'] = 9999999999;
            $results[] = $v;
        }
        $this->results = $results;
        $this->tpl_display('user/group_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'group_name' => trim(vds_request('group_name', '', 'post')),
                'min_exp' => intval(vds_request('min_exp', 0, 'post')),
                'discount_rate' => intval(vds_request('discount_rate', 100, 'post')),
            );
            
            $group_model = new user_group_model();
            $verifier = $group_model->verifier($data);
            if(TRUE === $verifier)
            {
                $group_model->create($data);
                $this->prompt('success', '添加用户组成功', url($this->MOD.'/user_group', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('user/group.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $group_id = vds_request('id');
            $data = array
            (
                'group_name' => trim(vds_request('group_name', '', 'post')),
                'min_exp' => intval(vds_request('min_exp', 0, 'post')),
                'discount_rate' => intval(vds_request('discount_rate', 100, 'post')),
            );
            
            $group_model = new user_group_model();
            $group = $group_model->find(array('group_id' => $group_id));
            if($group['min_exp'] == 0 && $data['min_exp'] != 0) $this->prompt('error', '缺少经验值下限为 0 的用户组', url($this->MOD.'/user_group', 'index'));
            $rule_slices = array();
            if($group['min_exp'] == $data['min_exp']) $rule_slices['min_exp'] = FALSE; //如未修改经验值下限
            
            $verifier = $group_model->verifier($data, $rule_slices);
            if(TRUE === $verifier)
            {
                $condition = array('group_id' => $group_id);
                if($group_model->update($condition, $data) > 0) $this->prompt('success', '更新用户组成功', url($this->MOD.'/user_group', 'index'));
                $this->prompt('error', '更新用户组失败');
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $group_model = new user_group_model();
            if($this->rs = $group_model->find(array('group_id' => vds_request('id'))))
            {
                $this->tpl_display('user/group.html');
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
        $group_model = new user_group_model();
        $group = $group_model->find(array('group_id' => $id));
        
        if($group['min_exp'] == 0) $this->prompt('error', '不能删除经验值下限为 0 的用户组', url($this->MOD.'/user_group', 'index'));
        
        if($group_model->delete(array('group_id' => $id)) > 0) $this->prompt('success', '删除用户组成功', url($this->MOD.'/user_group', 'index'));

        $this->prompt('error', '删除用户组失败');
    }
}