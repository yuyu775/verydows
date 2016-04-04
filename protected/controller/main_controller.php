<?php
class main_controller extends general_controller
{
    public function action_index()
    {   
        $this->home = array
        (
            'title' => $GLOBALS['cfg']['home_title'],
            'meta_keywords' => $GLOBALS['cfg']['home_keywords'],
            'meta_description' => $GLOBALS['cfg']['home_description'],
            'nav' => $GLOBALS['instance']['cache']->nav_model('get_site_nav'),
            'hot_searches' => explode(',', $GLOBALS['cfg']['goods_hot_searches']),
        );
        $this->newarrival = $GLOBALS['instance']['cache']->goods_model('find_goods', array(array('newarrival' => 1), $GLOBALS['cfg']['home_newarrival_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->recommend = $GLOBALS['instance']['cache']->goods_model('find_goods', array(array('recommend' => 1), $GLOBALS['cfg']['home_recommend_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->bargain = $GLOBALS['instance']['cache']->goods_model('find_goods', array(array('bargain' => 1), $GLOBALS['cfg']['home_bargain_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->latest_article = $GLOBALS['instance']['cache']->article_model('get_latest_article', array($GLOBALS['cfg']['home_article_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->tpl_display('index.html');
    }
    
    public function action_404()
    {
        $this->tpl_display('404.html');
    }
}