<?php
/**
 * OAuth2.0 Tencent QQ
 * @author Cigery
 */
class qq extends abstract_oauth
{
    private $api_uri = 'https://graph.qq.com/';
    
    public function create_login_url($state = '')
    {
        $params = array
        (
            'response_type' => 'code',
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $this->config['callback'],
            'state' => $state,
            'scope' => 'get_user_info',
        );
        return $this->api_uri.'oauth2.0/authorize?'.http_build_query($params);
    }
    
    public function check_callback($args)
    {
        if(empty($args['state']) || $args['state'] != $this->get_session('state')) return FALSE;
        
        $params = array
        (
            'grant_type' => 'authorization_code',
            'client_id' => $this->config['app_id'],
            'redirect_uri' => $this->config['callback'],
            'client_secret' => $this->config['app_key'],
            'scope' => 'get_user_info',
            'code' => $args['code'],
        );
        
        $uri = $this->api_uri.'oauth2.0/token?'.http_build_query($params);
        if($response = file_get_contents($uri))
        {
            
            if(strpos($response, 'callback') !== FALSE)
            {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response = substr($response, $lpos + 1, $rpos - $lpos -1);
            }
            
            $res = array();
            parse_str($response, $res);
            if(isset($res['access_token']))
            {
                $this->set_session('access_token', $res['access_token']);
                return $res;
            }
        }
        
        return FALSE;
    }
    
    public function get_oauth_key()
    {
        $uri = $this->api_uri.'oauth2.0/me?access_token='.$this->get_session('access_token');
        $response = file_get_contents($uri);
        if(strpos($response, "callback") !== FALSE)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($response);
        if(!isset($user->error))
        {
            $this->set_session('oauth_key', $user->openid);
            return $user->openid;
        }
        return FALSE;
    }
    
    public function get_user_info()
    {
        $params = array
        (
            'oauth_consumer_key' => $this->config['app_id'],
            'access_token' => $this->get_session('access_token'),
            'openid' => $this->get_session('oauth_key'),
            'format' => 'json',
        );
        
        $uri = $this->api_uri.'user/get_user_info?'.http_build_query($params);
        if($res = file_get_contents($uri))
        {
            $res = json_decode($res, TRUE);
            if($res['gender'] == '男') $res['gender'] = 1; elseif($res['gender'] == '女') $res['gender'] = 2; else $res['gender'] = 0;
            return array
            (
                'name' => $res['nickname'],
                'gender' => $res['gender'],
                'avatar' => $res['figureurl_qq_2'],
            );
        }
        return FALSE;
    }
}
