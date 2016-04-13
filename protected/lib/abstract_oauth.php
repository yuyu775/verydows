<?php
abstract class abstract_oauth
{
    protected $config = array();
    
    public function __construct($params = null)
    {
        $party = get_class($this);
        $this->set_session('party', $party);
        if(!empty($params)) $this->config = json_decode($params, TRUE);
        $this->config['callback'] = "{$GLOBALS['cfg']['http_host']}/oauth/callback/".$party;
    }
    
    abstract protected function create_login_url($state = '');
    
    abstract protected function check_callback($args);
    
    abstract protected function get_oauth_key();
    
    abstract protected function get_user_info();
    
    protected function http_post($uri, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Verydows Oauth2.0');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        if(version_compare(PHP_VERSION, '5.4.0', '<'))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        }
        else
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    protected function set_session($key, $value)
    {
        $_SESSION['oauth'][$key] = $value;
    }
    
    protected function get_session($key)
    {
        if(isset($_SESSION['oauth'][$key])) return $_SESSION['oauth'][$key];
        return null;
    }
}
