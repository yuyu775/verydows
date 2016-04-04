<?php
class user_oauth_model extends Model
{
    public $table_name = 'user_oauth';
    
    public function is_authorized($party, $oauth_key)
    {
        if($user_id = $this->find(array('party' => $party, 'oauth_key' => $oauth_key), null, 'user_id'))
        {
            $actinfo_model = new user_actinfo_model();
            $user = $actinfo_model->find(array('user_id' => $user_id['user_id']), null, 'user_id, last_date, last_ip');
            $_SESSION['user'] = array('user_id' => $user['user_id'], 'last_date' => $user['last_date'], 'last_ip' => $user['last_ip']);
            return TRUE;
        }
        return FALSE;
    }
}