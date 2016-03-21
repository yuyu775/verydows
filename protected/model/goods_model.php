<?php
class goods_model extends Model
{
    public $table_name = 'goods';
    
    public $rules = array
    (
        'goods_name' => array
        (
            'is_required' => array(TRUE, '商品名称不能为空'),
            'max_length' => array(180, '标题不能超过180个字符'),
        ),
        'goods_sn' => array
        (
            'max_length' => array(20, '商品货号不能超过20个字符'),
        ),
        'now_price' => array
        (
            'is_required' => array(TRUE, '当前售价不能为空'),
            'is_decimal' => array(TRUE, '当前售价格式不正确'),
        ),
        'original_price' => array
        (
            'is_decimal' => array(TRUE, '原售价格式不正确'),
        ),
        'stock_qty' => array
        (
            'is_nonegint' => array(TRUE, '库存数量必须是非负整数'),
        ),
        'goods_weight' => array
        (
            'is_decimal' => array(TRUE, '重量格式不正确'),
        ),
    );
    
    /**
     * 按条件查找商品
     */
    public function find_goods($conditions = array(), $limit = null)
    {
        $where = 'WHERE status = 1';
        $binds = array();
        if(!empty($conditions['cate']))
        {
            $where .= " AND (cate_id = :cate OR cate_id IN (SELECT cate_id FROM {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}goods_cate WHERE parent_id = :cate))";
            $binds[':cate'] = $conditions['cate'];
        }
        if(!empty($conditions['brand']))
        {
            $where .= ' AND brand_id = :brand';
            $binds[':brand'] = $conditions['brand'];
        }
        if(!empty($conditions['newarrival']))
        {
            $where .= ' AND newarrival = 1';
        }
        if(!empty($conditions['popular']))
        {
            $where .= ' AND popular = 1';
        }
        if(!empty($conditions['bargain']))
        {
            $where .= ' AND bargain = 1';
        }
        if(!empty($conditions['minpri']))
        {
            $where .= ' AND now_price >= :minpri';
            $binds[':minpri'] = $conditions['minpri'];
        }
        if(!empty($conditions['maxpri']))
        {
            $where .= ' AND now_price <= :maxpri';
            $binds[':maxpri'] = $conditions['maxpri'];
        }
        if(isset($conditions['kw']) && $conditions['kw'] != '')
        {
            if($GLOBALS['cfg']['goods_fulltext_query'] == 1)
            {
                $where .= ' AND MATCH (goods_name,meta_keywords) AGAINST (:kw IN BOOLEAN MODE)';
                $binds[':kw'] = $conditions['kw'];
            }
            else
            {
                $where .= ' AND goods_name LIKE :kw';
                $binds[':kw'] = '%'.$conditions['kw'].'%';
            }
        }
        if(!empty($conditions['att']))
        {
            $att = explode('@', urldecode($conditions['att']));
            $newatt = array();
            foreach($att as $v) if(!empty($v)) $newatt[substr($v, 0, strpos($v, '_'))] = substr($v, strpos($v, '_') + 1);
            $goods_attr_model = new goods_attr_model();
            $relax_atids = array();
            foreach($newatt as $k => $v)
            {
                if($gatids = $goods_attr_model->find_all(array('attr_id' => $k, 'value' => $v), null, 'goods_id')) 
                {
                    foreach($gatids as $v) $relax_atids[$k][] = $v['goods_id'];
                }
                else
                {
                    $relax_atids[$k] = array();
                }
            }
            sort($relax_atids);
            $strict_atids = vds_mult_array_intersect($relax_atids);
            $strict_atids = $strict_atids === FALSE ? $relax_atids[0] : $strict_atids;
            $attr_ids = !empty($strict_atids) ? implode(',', $strict_atids) : 0;
            $where .= " AND goods_id IN ({$attr_ids})";
        }
        
        if(is_array($limit))
        {
            $total = $this->query("SELECT COUNT(*) as count FROM {$this->table_name} {$where}", $binds);
            $limit = $limit + array(1, 10, 10);
            $this->pager($limit[0], $limit[1], 10, $total[0]['count']);
            $limit = $this->pager_section();
        }
        else
        {
            $limit = !empty($limit) ? ' LIMIT '.$limit : '';
        }
        
        if(isset($conditions['sort']))
        {
            $sort_map = array('goods_id DESC', 'now_price ASC', 'now_price DESC', 'created_date DESC', 'created_date ASC');
            $sort = isset($sort_map[$conditions['sort']]) ? $sort_map[$conditions['sort']] : $sort_map[0];
        }
        else
        {
            $sort = 'goods_id DESC';
        }
        
        $fields = 'goods_id, goods_name, original_price, now_price, goods_image';
        $sql = "SELECT {$fields} FROM {$this->table_name} {$where} ORDER BY {$sort} {$limit}";
        return $this->query($sql, $binds);
    }
    
    /**
     * 获取猜你喜欢的商品
     */
    public function get_guess_like()
    {
        $results = array();
        if($history_ids = vds_request('vds_history', null, 'cookie'))
        {
            $ids = array();
            $history = array_slice(explode(',', $history_ids), 0, 5);
            foreach($history as $k => $v) $ids[$k + 1] = intval($v);
            $questionmarks = str_repeat('?,', count($ids) - 1) . '?';
            
            $related_model = new goods_related_model();
            $sql = "SELECT goods_id, goods_name, original_price, now_price, goods_image
                    FROM {$this->table_name}
                    WHERE status = 1 AND
                          goods_id in (SELECT goods_id FROM {$related_model->table_name} WHERE related_id in ({$questionmarks}))
                    ORDER BY goods_id DESC
                   ";
                   
            $results = $this->query($sql, $ids);
        }
        
        return $results;
    }
    
    /**
     * 获取商品浏览历史
     */
    public function get_history()
    {
        $results = array();
        if($history = vds_request('vds_history', null, 'cookie'))
        {
            $ids = array();
            $history = array_slice(explode(',', $history), 0, $GLOBALS['cfg']['goods_history_num']);
            foreach($history as $k => $v) $ids[$k + 1] = intval($v);
            $questionmarks = str_repeat('?,', count($ids) - 1) . '?';
            $sql = "SELECT goods_id, goods_name, original_price, now_price, goods_image
                    FROM {$this->table_name}
                    WHERE goods_id in ({$questionmarks})
                   ";
            $results = $this->query($sql, $ids);
        }
        return $results;
    }
    
    /**
     * 获取相关联商品
     */
    public function get_related($goods_id, $limit = 5)
    {
        $sql = "SELECT goods_id, goods_name, original_price, now_price, goods_image
                FROM {$this->table_name}
                WHERE goods_id in (SELECT goods_id FROM {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}goods_related WHERE related_id = :goods_id)
                ORDER BY goods_id DESC LIMIT {$limit}
               "; 
        return $this->query($sql, array(':goods_id' => $goods_id));
    }
    
    /**
     * 商品销售排行
     */
    public function get_bestseller($cate_id = null, $limit = 10)
    {
        $where = "WHERE 1";
        if(!empty($cate_id)) $where .= " AND b.cate_id = {$cate_id}";
        $sql = "SELECT a.goods_id, COUNT(1) AS count, b.goods_name, b.now_price, b.goods_image
                FROM {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}order_goods AS a 
                LEFT JOIN {$this->table_name} AS b
                ON a.goods_id = b.goods_id
                {$where}
                GROUP BY a.goods_id
                ORDER BY COUNT(1) DESC
                LIMIT {$limit}
               ";
        return $this->query($sql);
        
    }
}
