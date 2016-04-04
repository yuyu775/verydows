<?php
class goods_cate_controller extends general_controller
{
    public function action_index()
    {
        $vcache = new vcache();
        $this->results = $vcache->goods_cate_model('indexed_cate_tree');
        $this->tpl_display('goods/cate_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'parent_id' => intval(vds_request('parent_id', 0, 'post')),
                'cate_name' => trim(vds_request('cate_name', '', 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
            );

            $cate_model = new goods_cate_model();
            $verifier = $cate_model->verifier($data);
            if(TRUE === $verifier)
            {
                $id = $cate_model->create($data);
                if($brands = vds_request('brands', array(), 'post'))
                {
                    $cate_brand_model = new goods_cate_brand_model();
                    foreach($brands as $v) $cate_brand_model->create(array('cate_id' => $id, 'brand_id' => $v));
                }
                self::clear_cache();
                $this->prompt('success', '添加商品分类成功', url($this->MOD.'/goods_cate', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $vcache = new vcache();
            $this->parent_select = $vcache->goods_cate_model('indexed_cate_tree');
            $this->brand_select = $vcache->brand_model('indexed_list');
            $this->tpl_display('goods/cate.html');
        }
    }

    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'parent_id' => intval(vds_request('parent_id', 0, 'post')),
                'cate_name' => trim(vds_request('cate_name', '', 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'seq' => vds_request('seq', 99, 'post'),
            );
            
            $cate_model = new goods_cate_model();
            $verifier = $cate_model->verifier($data);
            if(TRUE === $verifier)
            {
                $id = vds_request('id');
                $condition = array('cate_id' => $id);
                $cate_brand_model = new goods_cate_brand_model();
                $cate_brand_model->delete($condition);
                if($brands = vds_request('brands', array(), 'post'))
                {
                    foreach($brands as $v) $cate_brand_model->create(array('cate_id' => $id, 'brand_id' => $v));
                }
                if($cate_model->update($condition, $data) > 0) self::clear_cache();
                $this->prompt('success', '更新商品分类成功', url($this->MOD.'/goods_cate', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $id = vds_request('id');
            $cate_model = new goods_cate_model();
            if($this->rs = $cate_model->find(array('cate_id' => $id)))
            {
                $vcache = new vcache();
                $this->parent_select = $vcache->goods_cate_model('indexed_cate_tree');
                if($brand_list = $vcache->brand_model('indexed_list'))
                {
                    $cate_brand_model = new goods_cate_brand_model();
                    foreach($brand_list as $k => $v) 
                        $brand_list[$k]['checked'] = $cate_brand_model->find(array('cate_id' => $id, 'brand_id'=> $v['brand_id'])) ? 
                                                 'checked="checked"' : '';
                }
                $this->brand_select = $brand_list;
                
                $this->tpl_display('goods/cate.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }

    public function action_delete()
    {
        $id = vds_request('id');
        $cate_model = new goods_cate_model();
        if($cate_model->find_count(array('parent_id' => $id)) == 0)
        {
            $condition = array('cate_id' => $id);
            if($cate_model->delete($condition) > 0)
            {
                $cate_brand_model = new goods_cate_brand_model();
                $cate_brand_model->delete($condition);
                self::clear_cache();
                $this->prompt('success', '删除商品分类成功', url($this->MOD.'/goods_cate', 'index'));
            }  
            else
            {
                $this->prompt('error', '删除商品分类失败');
            }   
        }
        else
        {
            $this->prompt('error', '无法完成删除, 请先移除该分类下的子分类');
        }
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $vcache = new vcache();
        $vcache->goods_cate_model('indexed_cate_tree', null, -1);
    }
}