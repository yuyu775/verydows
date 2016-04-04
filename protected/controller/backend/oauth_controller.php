<?php
class oauth_controller extends general_controller
{
    public function action_index()
    {
        $oauth_model = new oauth_model();
        $this->results = $oauth_model->find_all();
        $this->tpl_display('oauth/oauth_list.html');
    }

    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'params' => vds_request('params', array(), 'post'),
                'enable' => intval(vds_request('enable', 0, 'post')),
            );

            $oauth_model = new oauth_model();
            $data['params'] = json_encode($data['params']);
            if($oauth_model->update(array('party' => vds_request('party')), $data) > 0)
            {
                self::clear_cache();
                $this->prompt('success', '更新成功', url($this->MOD.'/oauth', 'index'));
            }
            $this->prompt('error', '更新失败');
        }
        else
        {
            $oauth_model = new oauth_model();
            if($rs = $oauth_model->find(array('party' => vds_request('party'))))
            {
                $rs['template'] = 'backend/oauth/'.$rs['party'].'_params.html';
                $rs['params'] = json_decode($rs['params'], TRUE);
                $this->rs = $rs;
                $this->tpl_display('oauth/oauth.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $GLOBALS['instance']['cache']->oauth_model('indexed_list', null, -1);
    }
    
}