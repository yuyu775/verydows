<?php
class help_controller extends general_controller
{
	public function action_index()
    {
        $id = vds_request('id', null, 'get');
        $help_model = new help_model();
        if($this->help = $help_model->find(array('id' => $id)))
        {
            $vcache = new vcache();
            $this->cate_help_list = $vcache->help_model('cated_help_list');
            parent::tpl_display('help.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
}