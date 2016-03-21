<?php
class goods_controller extends general_controller
{
    public function action_index()
    {   
        if(vds_request('step') == 'search')
        {
            $cate_id = vds_request('cate_id', 0, 'post');
            $brand_id = vds_request('brand_id', 0, 'post');
            $sign_id = vds_request('sign_id', '', 'post');
            $status = vds_request('status', '', 'post');
            $kw = vds_request('kw', '', 'post');
    
            $where = 'WHERE 1';
            $binds = array();
            
            if(!empty($cate_id))
            {
                $where .= ' AND cate_id = :cate_id';
                $binds[':cate_id'] = $cate_id;
            }
            
            if(!empty($brand_id))
            {
                $where .= ' AND brand_id = :brand_id';
                $binds[':brand_id'] = $brand_id;
            }
            
            $sign_map = array('newarrival', 'recommend', 'bargain');
            if(isset($sign_map[$sign_id])) $where .= " AND {$sign_map[$sign_id]} = 1";
            
            if($status != '')
            {
                $where .= ' AND status = :status';
                $binds[':status'] = $status;
            }
    
            if($kw != '')
            {
                $where .= ' AND goods_name LIKE :kw';
                $binds[':kw'] = '%'.$kw.'%';
            }
            
            $goods_model = new goods_model();
            $total = $goods_model->query("SELECT COUNT(*) as count FROM {$goods_model->table_name} {$where}", $binds);
            if($total[0]['count'] > 0)
            {
                $fields = 'goods_id, goods_name, goods_sn, now_price, stock_qty, created_date, newarrival, recommend, bargain, status';
                $goods_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
                $limit = $goods_model->pager_section();
                
                $sort_id = vds_request('sort_id', 0, 'post');
                $sort_map = array('goods_id DESC', 'created_date DESC', 'created_date ASC', 'now_price DESC', 'now_price ASC');
                $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                
                $sql = "SELECT {$fields} FROM {$goods_model->table_name} {$where} ORDER BY {$sort} {$limit}";
                       
                $results = array
                (
                    'status' => 1,
                    'goods_list' => $goods_model->query($sql, $binds),
                    'paging' => $goods_model->page,
                );
            }
            else
            {
                $results = array('status' => 0);
            }
            
            echo json_encode($results);
        }
        else
        {
            $vcache = new vcache();
            $this->cateselect = $vcache->goods_cate_model('indexed_cate_tree');
            $this->brandselect = $vcache->brand_model('indexed_list');
            $this->tpl_display('goods/goods_list.html');
        }
    }

    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {   
            $data = array
            (
                'goods_name' => trim(vds_request('goods_name', '', 'post')),
                'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                'brand_id' => intval(vds_request('brand_id', 0, 'post')),
                'goods_sn' => trim(vds_request('goods_sn', '', 'post')),
                'now_price' => vds_request('now_price', '', 'post'),
                'original_price' => floatval(vds_request('original_price', '', 'post')),
                'goods_brief' => stripslashes(vds_request('goods_brief', '', 'post')),
                'goods_content' => stripslashes(vds_request('goods_content', '', 'post')),
                'stock_qty' => intval(vds_request('stock_qty', 0, 'post')),
                'goods_weight' => floatval(vds_request('goods_weight', 0, 'post')),
                'newarrival' => intval(vds_request('newarrival', 0, 'post')),
                'recommend' => intval(vds_request('recommend', 0, 'post')),
                'bargain' => intval(vds_request('bargain', 0, 'post')),
                'status' => intval(vds_request('status', 1, 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'created_date' => $_SERVER['REQUEST_TIME'],
            );
            
            $goods_model = new goods_model();
            $verifier = $goods_model->verifier($data);
            if(TRUE === $verifier)
            {
                $max_id = $goods_model->query("SELECT MAX(goods_id) AS id FROM {$goods_model->table_name}");
                $max_id = !empty($max_id[0]['id']) ? $max_id[0]['id'] : 1;
                //商品货号
                if(empty($data['goods_sn'])) $data['goods_sn'] = self::create_sn($data['cate_id'], $data['brand_id'], $max_id);
                
                //商品图片
                if(!empty($_FILES['goods_image']['name']))
                {
                    $img = self::upload_goods_image('goods_image', uniqid($max_id));
                    if($img['error'] == 'success') $data['goods_image'] = $img['name']; else $this->prompt('error', $img['error']);
                }
                
                $goods_id = $goods_model->create($data);
                //商品相册
                if(!empty($_FILES['goods_album']))
                {
                    $album_model = new goods_album_model();
                    $album_model->add_album_image('goods_album', $goods_id);
                }
                //购买选项
                if($opts = vds_request('goods_opts', null, 'post'))
                {
                    $optl_model = new goods_optional_model();
                    $optl_model->add_goods_optional($goods_id, $opts);
                }
                $this->prompt('success', '添加商品成功', url($this->MOD.'/goods', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $vcache = new vcache();
            $this->cate_select = $vcache->goods_cate_model('indexed_cate_tree');
            $this->brand_select = $vcache->brand_model('indexed_list');
            $this->opt_type_select = $vcache->goods_optional_type_model('indexed_list');
            $this->tpl_display('goods/goods.html');
        }
    }
    
    public function action_edit()
    {
        switch(vds_request('step'))
        {
            case 'submit':
            
                $goods_id = intval(vds_request('id'), 0);
                $data = array
                (
                    'goods_name' => trim(vds_request('goods_name', '', 'post')),
                    'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                    'brand_id' => intval(vds_request('brand_id', 0, 'post')),
                    'goods_sn' => vds_request('goods_sn', '', 'post'),
                    'now_price' => vds_request('now_price', '', 'post'),
                    'original_price' => floatval(vds_request('original_price', '', 'post')),
                    'goods_brief' => stripslashes(vds_request('goods_brief', '', 'post')),
                    'goods_content' => stripslashes(vds_request('goods_content', '', 'post')),
                    'stock_qty' => vds_request('stock_qty', 0, 'post'),
                    'goods_weight' => floatval(vds_request('goods_weight', 0, 'post')),
                    'newarrival' => intval(vds_request('newarrival', 0, 'post')),
                    'recommend' => intval(vds_request('recommend', 0, 'post')),
                    'bargain' => intval(vds_request('bargain', 0, 'post')),
                    'status' => intval(vds_request('status', 0, 'post')),
                    'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                    'meta_description' => trim(vds_request('meta_description', '', 'post')),
                    'created_date' => $_SERVER['REQUEST_TIME'],
                );
                if(empty($data['goods_sn'])) $data['goods_sn'] = self::create_sn($data['cate_id'], $data['brand_id'], $goods_id);
                
                $goods_model = new goods_model();
                $verifier = $goods_model->verifier($data);
                if(TRUE === $verifier)
                {
                    //商品图片
                    if(!empty($_FILES['goods_image']['name']))
                    {
                        $img = self::upload_goods_image('goods_image', uniqid($goods_id));
                        if($img['error'] == 'success') $data['goods_image'] = $img['name']; else $this->prompt('error', $img['error']);
                    }
                    
                    $condition = array('goods_id' => $goods_id);
                    if($goods_model->update($condition, $data) > 0)
                    {
                        //商品相册
                        $album_model = new goods_album_model();
                        if($album_removed = vds_request('album_removed', null, 'post'))
                        {
                            $album_removed = explode(',', $album_removed);
                            foreach($album_removed as $v) $album_model->delete(array('id' => $v));
                        }
                        if(!empty($_FILES['goods_album'])) $album_model->add_album_image('goods_album', $goods_id);
                        
                        //商品可选项
                        $opt_model = new goods_optional_model();
                        $opt_model->delete($condition);
                        if($opts = vds_request('goods_opts', null, 'post')) $opt_model->add_goods_optional($goods_id, $opts);
                        
                        $this->prompt('success', '更新商品成功', url($this->MOD.'/goods', 'index'));
                    }
                    else
                    {
                        $this->prompt('error', '更新商品失败');
                    }
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
                
            break;
            
            case 'attr':
                
                if(vds_request('do') == 'update')
                {
                    $goods_id = intval(vds_request('goods_id', 0));
                    $goods_attr_model = new goods_attr_model();
                    $goods_attr_model->delete(array('goods_id' => $goods_id));
                    $attrs = vds_request('attrs', array(), 'post');
                    if(isset($attrs['id']) && isset($attrs['value']) && $goods_attrs = array_combine($attrs['id'], $attrs['value']))
                    {
                        foreach($goods_attrs as $k => $v)
                        {
                            $v = trim($v);
                            if($v != '') $goods_attr_model->create(array('goods_id' => $goods_id, 'attr_id' => $k, 'value' => $v));
                        }
                        $this->prompt('success', '更新商品属性规格成功');
                    }
                    else
                    {
                        $this->prompt('error', '更新商品属性规格失败');
                    }
                }
                else
                {
                    $goods_id = intval(vds_request('id', 0));
                    $goods_model = new goods_model();
                    if($this->goods = $goods_model->find(array('goods_id' => $goods_id), null, 'goods_id, cate_id, goods_name, goods_sn'))
                    {
                        $vcache = new vcache();
                        $this->cates = $vcache->goods_cate_model('indexed_cate_tree');
                        $this->tpl_display('goods/goods_attr.html');
                    }
                    else
                    {
                        $this->prompt('error', '未找到相应的数据记录');
                    }
                }

            break;
            
            case 'related':
                
                if(vds_request('do') == 'update')
                {
                    $goods_id = intval(vds_request('id', 0));
                    $related_model = new goods_related_model();
                    $related_model->delete(array('goods_id' => $goods_id));
                    $related_model->delete(array('related_id' => $goods_id, 'direction' => 2));
                    if($related = vds_request('related', null, 'post')) $related_model->add_related($goods_id, $related);
                    $this->prompt('success', '更新关联商品成功');
                }
                else
                {
                    $goods_id = intval(vds_request('id', 0));
                    $goods_model = new goods_model();
                    if($this->goods = $goods_model->find(array('goods_id' => $goods_id), null, 'goods_id, goods_name, goods_sn'))
                    {
                        $vcache = new vcache();
                        $this->cateselect = $vcache->goods_cate_model('indexed_cate_tree');
                        $this->brandselect = $vcache->brand_model('indexed_list');
                        $related_model = new goods_related_model();
                        $this->related = $related_model->get_related_goods($goods_id);
                        $this->tpl_display('goods/goods_related.html');
                    }
                    else
                    {
                        $this->prompt('error', '未找到相应的数据记录');
                    }
                }
            
            break;
            
            case 'asyncgetattrs':
                
                $cate_id = intval(vds_request('cate_id', 0));
                $goods_id = intval(vds_request('goods_id', 0));
                $goods_attr_model = new goods_attr_model();
                if($attrs = $goods_attr_model->get_goods_attrs($cate_id, $goods_id)) echo json_encode(array('status' => 1, 'attrs' => $attrs));
                else echo json_encode(array('status' => 0, 'attrs' => ''));

            break;
            
            case 'asyncgetgoods':
            
                $cate_id = vds_request('cate_id', 0, 'post');
                $brand_id = vds_request('brand_id', 0, 'post');
                $kw = vds_request('kw', '', 'post');
                $where = 'WHERE 1';
                $binds = array();
                if(!empty($cate_id))
                {
                    $where .= ' AND cate_id = :cate_id';
                    $binds[':cate_id'] = $cate_id;
                }
                if(!empty($brand_id))
                {
                    $where .= ' AND brand_id = :brand_id';
                    $binds[':brand_id'] = $brand_id;
                }
                if($kw != '')
                {
                    $where .= ' AND (goods_name LIKE :kw OR goods_sn = :sn)';
                    $binds[':kw'] = '%'.$kw.'%';
                    $binds[':sn'] = $kw;
                }
                
                $goods_model = new goods_model();
                $sql = "SELECT goods_id, goods_name
                        FROM {$goods_model->table_name} {$where}
                        ORDER BY goods_id DESC
                       ";
                
                if($goods = $goods_model->query($sql, $binds)) $results = array('status' => 1, 'goods' => $goods);
                else $results = array('status' => 0);
                echo json_encode($results);
                
            break;
            
            default:
                
                $goods_id = intval(vds_request('id', 0));
                $condition = array('goods_id' => $goods_id);
                $goods_model = new goods_model();
                
                if($this->rs = $goods_model->find($condition))
                {
                    $vcache = new vcache();
                    $this->cate_select = $vcache->goods_cate_model('indexed_cate_tree');
                    $this->brand_select = $vcache->brand_model('indexed_list');
                    $this->opt_type_select = $vcache->goods_optional_type_model('indexed_list');
                    //获取商品相册
                    $album_model = new goods_album_model();
                    $this->album_list = $album_model->find_all($condition);
                    //获取购买选项
                    $opt_model = new goods_optional_model();
                    $this->opt_list = $opt_model->get_goods_optional($goods_id);
                    $this->tpl_display('goods/goods.html');
                }
                else
                {
                    $this->prompt('error', '未找到相应的数据记录');
                }
        }
    }
    
    public function action_editor()
    {
        $save_path = 'upload'.DS.'goods'.DS.'editor'.DS;
        $uploader = new uploader($save_path);
        $file = $uploader->upload_file('upfile');
        if ($file['error'] == 'success')
        {
            $callback = vds_request('callback');
            $rs = array('state' => 'SUCCESS', 'url' => $file['url']);
            if($callback) echo '<script>'.$callback.'('.json_encode($rs).')</script>';
            echo json_encode($rs);
        }
        else
        {
            echo "<script>alert('{$file['error']}')</script>";
        }
    }

    public function action_delete()
    {
        $id = vds_request('id');
        $condition = array('goods_id' => $id);
        $goods_model = new goods_model();
        
        if(($goods_model->delete($condition)) > 0)
        {
            //删除相册数据
            $album_model = new goods_album_model();
            $album_model->delete($condition);
            //删除商品选项
            $optl_model = new goods_optional_model();
            $optl_model->delete($condition);
            //删除关联商品
            $related_model = new goods_related_model();
            $related_model->delete($condition);
            $related_model->delete(array('related_id' => $id));
            //删除商品属性
            $attr_model = new goods_attr_model();
            $attr_model->delete($condition);
            //删除商品评价
            $review_model = new goods_review_model();
            $review_model->delete($condition);
            
            $this->prompt('success', '删除商品成功', url($this->MOD.'/goods', 'index'));
        }
        else
        {
            $this->prompt('error', '删除商品失败');
        }
    }
    
    private static function create_sn($cate_id = 0, $brand_id = 0, $goods_id)
    {
        $sn = str_pad($cate_id, 3, 0, STR_PAD_LEFT). str_pad($brand_id, 3, 0, STR_PAD_LEFT) . $goods_id;
        $sn .= str_pad(mt_rand(0, 999), 3, 0, STR_PAD_LEFT);
        return $sn;
    }
    
    private static function upload_goods_image($file_input, $save_name)
    {
        $save_path = 'upload'.DS.'goods'.DS.'image'.DS;
        $format_limit = empty($GLOBALS['cfg']['upload_goods_filetype']) ? null : explode('|', $GLOBALS['cfg']['upload_goods_filetype']);
        $size_limit = empty($GLOBALS['cfg']['upload_goods_filesize']) ? null : vds_size_to_bytes($GLOBALS['cfg']['upload_goods_filesize']);
        $uploader = new uploader($save_path, $format_limit, $size_limit);
        return $uploader->upload_file($file_input, $save_name);
    }
}