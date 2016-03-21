<?php
class main_controller extends general_controller
{
    public function action_index()
    {   
        $vcache = new vcache();
        $this->home = array
        (
            'title' => $GLOBALS['cfg']['home_title'],
            'meta_keywords' => $GLOBALS['cfg']['home_keywords'],
            'meta_description' => $GLOBALS['cfg']['home_description'],
            'nav' => $vcache->nav_model('get_site_nav'),
            'hot_searches' => explode(',', $GLOBALS['cfg']['goods_hot_searches']),
        );
        $this->newarrival = $vcache->goods_model('find_goods', array(array('newarrival' => 1), $GLOBALS['cfg']['home_newarrival_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->recommend = $vcache->goods_model('find_goods', array(array('recommend' => 1), $GLOBALS['cfg']['home_recommend_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->bargain = $vcache->goods_model('find_goods', array(array('bargain' => 1), $GLOBALS['cfg']['home_bargain_num']), $GLOBALS['cfg']['data_cache_lifetime']);
        $this->latest_article = $vcache->article_model('get_latest_article', array($GLOBALS['cfg']['home_article_num']), $GLOBALS['cfg']['data_cache_lifetime']);

        parent::tpl_display('index.html');
    }
    
    public function action_404()
    {
        parent::tpl_display('404.html');
    }
}