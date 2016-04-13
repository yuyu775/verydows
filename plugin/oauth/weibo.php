<?php
/**
 * OAuth2.0 Sina Weibo
 * @author Cigery
 */
class weibo extends abstract_oauth
{
    private $api_uri = 'https://api.weibo.com/';
    
    public function create_login_url($state = '')
    {
        $params = array
        (
            'client_id' => $this->config['app_key'],
            'client_secret' => $this->config['app_secret'],
            'redirect_uri' => $this->config['callback'],
            'response_type' => 'code',
            'state' => $state,
            'display' => '',
        );
        return $this->api_uri.'oauth2/authorize?'.http_build_query($params);
    }
    
    public function check_callback($args)
    {
        if(empty($args['state']) || $args['state'] != $this->get_session('state')) return FALSE;
        
        $params = array
        (
            'client_id' => $this->config['app_key'],
            'client_secret' => $this->config['app_secret'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config['callback'],
            'code' => isset($args['code']) ? $args['code'] : '',
        );
        
        $uri = $this->api_uri.'oauth2/access_token';
        if($response = $this->http_post($uri, $params))
        {
            $res = json_decode($response, TRUE);
            if(!isset($res['error']))
            {
                $this->set_session('access_token', $res['access_token']);
                return $res['access_token'];
            }
        }
        return FALSE;
    }
    
    public function get_oauth_key()
    {
        $uri = $this->api_uri.'2/account/get_uid.json?access_token='.$this->get_session('access_token');
        if($response = file_get_contents($uri))
        {
            $res = json_decode($response, TRUE);
            $this->set_session('oauth_key', $res['uid']);
            return $res['uid'];
        }
        return FALSE;
    }
    
    public function get_user_info()
    {
        $params = array
        (
            'access_token' => $this->get_session('access_token'),
            'uid' => $this->get_session('oauth_key'),
        );
        
        $uri = $this->api_uri.'2/users/show.json?'.http_build_query($params);
        if($res = file_get_contents($uri))
        {
            $res = json_decode($res, TRUE);
            if($res['gender'] == 'm') $res['gender'] = 1; elseif($res['gender'] == 'f') $res['gender'] = 2; else $res['gender'] = 0;
            return array
            (
                'name' => $res['screen_name'],
                'gender' => $res['gender'],
                'avatar' => $res['avatar_large'],
            );
        }
        return FALSE;
    }
}
