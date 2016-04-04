<?php
class oauth_model extends Model
{
    public $table_name = 'oauth';
    
    public function indexed_list()
    {
        $res = array();
        if($find_all = $this->find_all(array('enable' => 1))) 
        {
            foreach($find_all as $v)
            {
                $res[$v['party']] = $v;
                $oauth_obj = plugin::instance('oauth', $v['party'], array($v['params']), TRUE);
                $res[$v['party']]['login_url'] = $oauth_obj->create_login_url();
                unset($res[$v['party']]['party']);
            }
        }
        return $res;
    }
}