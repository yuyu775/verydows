<?php
function reviser_truncate($string, $length = 30, $replace = '...')
{
    if(mb_strlen($string, 'utf-8') > $length)
    {
        $length -= min($length, strlen($replace));
        return mb_substr($string, 0, $length, 'utf-8') . $replace;
    }
    return $string;
}