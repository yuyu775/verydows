<?php
/**
  * 接收HTTP变量
 */
function vds_request($name, $default = FALSE, $method = 'request')
{
    switch($method)
    {
        case 'get': $value = isset($_GET[$name]) ? $_GET[$name]: FALSE; break;
        case 'post': $value = isset($_POST[$name]) ? $_POST[$name] : FALSE; break;
        case 'cookie': $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : FALSE; break;
        case 'request': 
        default:
            $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : FALSE;
        break;
   }
   if(FALSE === $value) return $default;
   return $value;
}
    
/**
 * 页面跳转
 */
function vds_jump($url, $delay = 0)
{
    echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
    exit;
}

/**
 * 加密字符串
 */
function vds_encrypt($val)
{
    if(strlen($val) > 0 && strlen($GLOBALS['cfg']['encrypt_key']) > 0)
    {
        $val = base64_encode($val);
        $key = sha1($GLOBALS['cfg']['encrypt_key']);
        if(strlen($val) > strlen($key)) $key = str_pad($key, strlen($val), $key, STR_PAD_RIGHT);
        $val_arr = str_split($val);
        $key_arr = str_split($key);
        foreach ($val_arr as $k => $v) $en[] = ord($v) + ord($key_arr[$k]);
        return strrev(implode('.', $en));
    }
    return FALSE;
}
    
/**
 * 解密字符串
 */
function vds_decrypt($val)
{   
    if(strlen($val) > 0 && strlen($GLOBALS['cfg']['encrypt_key']) > 0)
    {
        $key = sha1($GLOBALS['cfg']['encrypt_key']);
        $key_arr = str_split($key);
        $val_arr = explode('.', strrev($val));
        if(count($val_arr) > count($key_arr)) $key_arr = str_split(str_pad($key, count($val_arr), $key, STR_PAD_RIGHT));
        $de = '';
        foreach($val_arr as $k => $v) $de .= chr($v - ord($key_arr[$k]));
        return base64_decode($de);
    }
    return FALSE;
}
    
/**
 * 兼容PHP5.5 array_column() 函数方法
 */
function vds_array_column(array $input, $column_key = null, $index_key = null)
{
    if(function_exists('array_column')) return array_column($input, $column_key, $index_key);
    $results = array();
    foreach($input as $item)
    {
        if(!is_array($item)) continue;
        if(is_null($column_key)) $value = $item; else $value = $item[$column_key];
        if(!is_null($index_key))
        {
            $key = $item[$index_key];
            $results[$key] = $value;
        }
        else
        {
            $results[] = $value;
        }
    }
    return $results;
}
    
/**
 * 随机字符
 */
function vds_random_chars($length = 20, $is_numeric = FALSE)
{
    $hex = base_convert(md5(microtime().$GLOBALS['cfg']['http_host']), 16, $is_numeric ? 10 : 35);
    $hex = $is_numeric ? (str_replace('0', '', $hex).'012340567890') : ($hex.'zZ'.strtoupper($hex));
    $random = '';
    if(!$is_numeric)
    {
        $random = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length --;
    }
    for($i = 0; $i < $length; $i++) $random .= $hex{mt_rand(0, strlen($hex) - 1)};
    return $random;
}
	
/**
 * 获取用户ip地址
 */
function vds_get_ip()
{
    $ip = '0.0.0.0';
    $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : null;
    $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
    else $ip = $remote;
    return $ip;
}
    
/**
 * 字节转换成具体单位
 */
function vds_bytes_to_size($size, $unit = 'B', $decimals = 2, $target_unit = 'auto')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $the_unit = array_search(strtoupper($unit), $units); 
    if($target_unit != 'auto')
    $target_unit = array_search(strtoupper($target_unit), $units);
    while($size >= 1024)
    {
        $size /= 1024;
        $the_unit++;
        if($the_unit == $target_unit) break;
    }
    return sprintf("%1\$.{$decimals}f", $size) . ' ' . $units[$the_unit];
}
/**
 * 具体单位转换成字节
 */
function vds_size_to_bytes($str)
{
    $str = strtoupper(str_replace(' ', '', $str));
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unit = preg_replace('/[^A-Z]/', '', $str); 
    $size = preg_replace('/[^0-9.]/', '', $str); 
    $target_unit = array_search($unit, $units);
    $target_unit = empty($target_unit) ? 0 : $target_unit;
    return round($size * pow(1024, $target_unit));
}
    
/**
 * 数组分页
 */
function vds_array_paging($data, $page_no = 1, $per_qty = 10, $scope = 10)
{
    $start = ($page_no - 1) * $per_qty;
    $end = $start + $per_qty;
    $count = count($data);
    $results = array('slice' => array(), 'pagination' => array());
    if($start < 0 || $count <= $start) return $results;
    if($count <= $end) $results['slice'] = array_slice($data, $start);
    else $results['slice'] = array_slice($data, $start, $end - $start);
    if($count > $per_qty)
    { 
        $total_page = ceil($count / $per_qty);
        $page_no = min(intval(max($page_no, 1)), $count);
        $pagination = array
        (
            'total_count' => $count, 
            'page_size'   => $per_qty,
            'total_page'  => $total_page,
    		'first_page'  => 1,
    		'prev_page'   => (( 1 == $page_no ) ? 1 : ($page_no - 1)),
    		'next_page'   => (( $page_no == $total_page ) ? $total_page : ($page_no + 1)),
    		'last_page'   => $total_page,
    		'current_page'=> $page_no,
    		'all_pages'   => array(),
            'scope'       => $scope,
    		'offset'      => ($page_no - 1) * $per_qty,
    		'limit'       => $per_qty,
        );
        if($total_page <= $scope) $pagination['all_pages'] = range(1, $total_page);
        else if($page_no <= $scope/2) $pagination['all_pages'] = range(1, $scope);
        else if($page_no <= $total_page - $scope/2 ) $pagination['all_pages'] = range(($page_no + intval($scope/2))- $scope + 1, $page_no + intval($scope/2));
   	    else $pagination['all_pages'] = range($total_page - $scope + 1, $total_page);
        $results['pagination'] = $pagination;
    }
    return $results;
}
    
/**
 * 随机取出数组单元
 */
function vds_array_range(array $input, $num = 1)
{
    if(count($input) >= $num) return array_rand($input, $num);
    return $input;
}
    
/**
 * 计算n个数组的交集
 */
function vds_mult_array_intersect($arrays)
{
    $count = count($arrays);
    if($count >= 2)
    {
        $array_tmp =  $arrays[0];
        for($i = 1; $i < $count; $i++) $array_tmp = array_intersect($array_tmp, $arrays[$i]);
        return $array_tmp;
    }
    return FALSE;
}
?>
