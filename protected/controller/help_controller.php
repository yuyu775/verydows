<?php
class help_controller extends general_controller
{
    public function action_index()
    {
        $id = vds_request('id', null, 'get');
        $help_model = new help_model();
        if($this->help = $help_model->find(array('id' => $id)))
        {
            $this->cate_help_list = $GLOBALS['instance']['cache']->help_model('cated_help_list');
            $this->tpl_display('help.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
}