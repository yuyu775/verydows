<?php
class goods_controller extends general_controller
{
    public function action_index()
    {
        $id = intval(vds_request('id', 0, 'get'));
        $condition = array('goods_id' => $id);
        $goods_model = new goods_model();

        if($goods = $goods_model->find($condition))
        {
            $cate_model = new goods_cate_model();
            $this->breadcrumbs = $cate_model->breadcrumbs($goods['cate_id']);
            
            $this->goods = $goods;
            //商品相册
            $album_model = new goods_album_model();
            $this->album_list = $album_model->find_all($condition);
            //购买选择项
            $optl_model = new goods_optional_model();
            $this->opt_list = $optl_model->get_goods_optional($id);
            //商品规格
            $attr_model = new goods_attr_model();
            $this->specs = $attr_model->get_goods_specs($goods['cate_id'], $id);
            //关联商品
            $this->related = $goods_model->get_related($id, $GLOBALS['cfg']['goods_related_num']);
            //热销商品
            $vcache = new vcache();
            $this->bestseller = $vcache->goods_model('get_bestseller', null, 10, $GLOBALS['cfg']['data_cache_lifetime']);
            //保存浏览历史
            self::set_history($id);
            
            parent::tpl_display('goods.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_search()
    {
        $conditions = array
        (
            'cate' => intval(vds_request('cate', 0, 'get')),
            'brand' => intval(vds_request('brand', 0, 'get')),
            'minpri' => intval(vds_request('minpri', 0, 'get')),
            'maxpri' => intval(vds_request('maxpri', 0, 'get')),
            'kw' => trim(vds_request('kw', '', 'get')),
            'sort' => intval(vds_request('sort', 0, 'get')),
            'page' => intval(vds_request('page', 1, 'get')),
        );

        $goods_model = new goods_model();
        $this->goods_list = $goods_model->find_goods($conditions, array(vds_request('page', 1), $GLOBALS['cfg']['goods_search_per_num']));
        $this->goods_paging = $goods_model->page;
        $this->filters = self::set_search_filters($conditions['kw']);
        $this->guess_likes = $goods_model->get_guess_like();
        $this->history = $goods_model->get_history();
        $this->u = $conditions;
        
        parent::tpl_display('search.html');
    }
    
    /**
     * 设置搜索筛选项
     */
    private static function set_search_filters($kw)
    {
        $filters = array();
        $where = 'WHERE status = 1 AND goods_name LIKE :kw';
        $binds = array(':kw' => '%'.$kw.'%');
        
        $model = new Model();
        $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
        //分类筛选
        $cate_sql = "SELECT cate_id, cate_name, parent_id
                     FROM {$tblpre}goods_cate
                     WHERE cate_id in (SELECT cate_id FROM {$tblpre}goods {$where})
                     GROUP BY cate_id
                    ";
        $filters['cate'] = $model->query($cate_sql, $binds);          
        //品牌筛选
        $brand_sql = "SELECT brand_id, brand_name
                      FROM {$tblpre}brand
                      WHERE brand_id in (SELECT brand_id FROM {$tblpre}goods {$where})
                      GROUP BY brand_id
                     ";
        $filters['brand'] = $model->query($brand_sql, $binds);
        
        //价格筛选
        $filters['price'] = array();
        $pri_sql = "SELECT count(goods_id) AS count, min(now_price) AS min, max(now_price) AS max
                    FROM {$tblpre}goods {$where}
                   ";
        if($pri_query = $model->query($pri_sql, $binds))
        {
            $pri_max_num = round($pri_query[0]['count'] / 10);
            if($pri_max_num >= 2)
            {
                if($pri_max_num >= 6) $pri_max_num = 6;
                $pri_incr = ceil(($pri_query[0]['max'] - $pri_query[0]['min']) / $pri_max_num);
                for ($i = 1; $i <= $pri_max_num; $i++)
                {
                    $l = $pri_incr * ($i - 1) + 1;
                    $r = $pri_incr * $i;
                    
                    if($i == 1) $min = 0; else $min = intval(str_pad(substr($l, 0, 2), strlen($l), 9, STR_PAD_RIGHT));
                    if($i == $pri_max_num)
                    {
                        $max = 0;
                        $str = $min.'以上';
                    }
                    else
                    {
                        $max = intval(str_pad(substr($r, 0, 2), strlen($r), 9, STR_PAD_RIGHT));
                        $str = $min.'-'.$max;
                    }
                    $filters['price'][] = array('min' => $min, 'max' => $max, 'str' => $str);
                }
            }
        }

        return $filters;
    }
    
    /**
     * 设置商品浏览历史
     * @param  $goods_id  商品ID
     */
    private static function set_history($goods_id, $num = 20)
    {
        if($history = vds_request('vds_history', null, 'cookie'))
        {
            $history = explode(',', $history);
            if(!in_array($goods_id, $history))
            {
                array_unshift($history, $goods_id);
                setcookie('vds_history', implode(',', array_slice($history, 0, $num)), time() + 86400 * 7, '/');
            }
        }
        else
        {
            setcookie('vds_history', $goods_id, time() + 86400 * 7, '/');
        }
    }

}