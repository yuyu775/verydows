<?php
class goods_cate_model extends Model
{
    public $table_name = 'goods_cate';
    
    public $rules = array
    (
        'cate_name' => array
        (
            'is_required' => array(TRUE, '分类名称不能为空'),
            'max_length' => array(60, '分类名称不能超过60个字符(30个中文字符)'),    
        ),
        'seq' => array
        (
            'is_seq' => array(TRUE, '排序必须为0-99之间的整数'),
        ),
    );
    
    /**
     * 获取分类树(以主键作为分类树数组索引)
     */
    public function indexed_cate_tree()
    {
        if($find_all = $this->find_all(null, 'seq ASC', 'cate_id, parent_id, cate_name, seq'))
        {
            $tree_until = new tree_util($find_all);
            return vds_array_column($tree_until->tree, null, 'cate_id');
        }
        return $find_all;
    }
    
    /**
     * 获取商品分类栏中的1、2级分类(只找顶级和其子分类)
     */
    public function goods_cate_bar()
    {
        $field = 'cate_id, cate_name';
        if($cates = $this->find_all(array('parent_id' => 0), 'seq ASC', $field))
        {
            foreach($cates as $k => $v) $cates[$k]['children'] = $this->find_all(array('parent_id' => $v['cate_id']), 'seq ASC', $field);
        }
        return $cates;
    }
    
    /**
     * 分类面包屑
     * @param  $cate_id  分类ID
     */
    public function breadcrumbs($cate_id)
    {
        $results = array();
        $cate = $this->find(array('cate_id' => $cate_id), null, 'cate_id, parent_id, cate_name');
        while(TRUE)
		{
			if(!empty($cate))
			{
				array_unshift($results, $cate);
				$cate = $this->find(array('cate_id' => $cate['parent_id']), null, 'cate_id, parent_id, cate_name');
			}
			else
			{
				break;
			}
		}
        return $results;
    }
    
    /**
     * 设置分类的筛选项
     * @param  $cate_id  分类ID
     */
    public function set_filters($cate_id, $att = '')
    {
        $filters = array();
        
        //品牌筛选
        $filters['brand'] = array();
        $cate_brand_model = new goods_cate_brand_model();
        if($cate_brands = $cate_brand_model->find_all(array('cate_id' => $cate_id), null, 'brand_id'))
        {
            $vcache = new vcache();
            $brands = $vcache->brand_model('indexed_list');
            foreach($cate_brands as $v) $filters['brand'][] = $brands[$v['brand_id']];
        }
        
        //属性筛选
        $cate_attr_model = new goods_cate_attr_model();
        if($filters['attr'] = $cate_attr_model->find_all(array('cate_id' => $cate_id, 'filtrate' => 1), 'seq ASC'))
        {
            $attarr = !empty($att) ? explode('@', urldecode($att)) : array();
            $newatt = array();
            foreach($attarr as $u) if(!empty($u)) $newatt[substr($u, 0, strpos($u, '_'))] = $u;
            
            $newattstr = !empty($newatt) ? implode('@', $newatt) : '';

            foreach($filters['attr'] as $k => $v)
            {
                if(!empty($v['opts']))
                {
                    $opts = json_decode($v['opts'], TRUE);
                    $filters['attr'][$k]['opts'] = array();
                    
                    foreach($opts as $kk => $vv)
                    {
                        $filters['attr'][$k]['opts'][$kk]['name'] = $vv . $v['uom'];
                        $filters['attr'][$k]['opts'][$kk]['att'] = urlencode($newattstr.'@'.$v['attr_id'].'_'.$vv);
                        $filters['attr'][$k]['opts'][$kk]['checked'] = 0;
                        if(in_array($v['attr_id'].'_'.$vv, $newatt)) $filters['attr'][$k]['opts'][$kk]['checked'] = 1;
                    }
                    
                    $filters['attr'][$k]['unlimit'] = array
                    (
                        'att' => urlencode(implode('@', array_diff_key($newatt, array($v['attr_id'] => '')))),
                        'checked' => isset($newatt[$v['attr_id']]) ? 0 : 1,
                    );
                    //array_unshift($filters['attr'][$k]['opts'], $unlimit);
                }
                else
                {
                    $filters['attr'][$k]['opts'] = array();
                }
            }
        }
        //价格筛选
        $filters['price'] = self::auto_price_zone($cate_id);
        
        return $filters;
    }
    
    /**
     * 自动智能价格分区
     * @param  $cate_id  分类ID
     * @param  $zone_qty 分区个数
     */
    private function auto_price_zone($cate_id, $zone_qty = 3)
    {
        $results = array();
        $goods_model = new goods_model();
        $sql = "SELECT now_price FROM {$goods_model->table_name} 
                WHERE cate_id = :cate OR cate_id in (SELECT cate_id FROM {$this->table_name} WHERE parent_id = :cate)
               ";
        if($goods = $goods_model->query($sql, array(':cate' => $cate_id)))
        {
            foreach($goods as $v) $prices[] = ceil($v['now_price']);
            $prices = array_unique($prices);
            sort($prices);
            $count = count($prices);
            $per = floor($count / $zone_qty);
            
            if($per > 1)
            {
                for ($i = 1; $i <= $zone_qty; $i++)
                {
                    $lk = $per * ($i - 1);
                    $hk = $per * $i;
                    
                    if($i == 1)
                    {
                        $min = 0;
                        $max = str_pad(substr($prices[$hk], 0, 2), strlen($prices[$hk]), 9, STR_PAD_RIGHT);
                        $str = '0-'.$max;
                    }
                    elseif($i == $zone_qty)
                    {
                        $min = intval(str_pad(substr($prices[$lk], 0, 2), strlen($prices[$lk]), 9, STR_PAD_RIGHT)) + 1;
                        $max = 0;
                        $str = $min.'以上';
                    }
                    else
                    {
                        $min = intval(str_pad(substr($prices[$lk], 0, 2), strlen($prices[$lk]), 9, STR_PAD_RIGHT)) + 1;
                        $max = str_pad(substr($prices[$hk], 0, 2), strlen($prices[$hk]), 9, STR_PAD_RIGHT);
                        $str = $min.'-'.$max;
                    }
                    $results[] = array('min' => $min, 'max' => $max, 'str' => $str);
                }
            }
        }

        return $results;
    }

    
}
