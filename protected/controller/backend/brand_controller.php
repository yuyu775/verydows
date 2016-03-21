<?php
class brand_controller extends general_controller
{
    public function action_index()
    {
        $vcache = new vcache();
        $this->results = $vcache->brand_model('indexed_list');
        $this->tpl_display('goods/brand_list.html');
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'brand_name' => trim(vds_request('brand_name', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );

            $brand_model = new brand_model();
            $verifier = $brand_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($_FILES['logo_file']['name']))
                {
                    $save_path = 'upload'.DS.'brand'.DS.'logo'.DS;
                    $uploader = new uploader($save_path);
                    $logo = $uploader->upload_file('logo_file');
                    if ($logo['error'] == 'success')
                    {
                        $data['brand_logo'] = $logo['url'];
                    }
                    else
                    {
                        $this->prompt('error', $logo['error']);
                    }
                }
                else
                {
                    $data['brand_logo'] = trim(vds_request('logo_src', '', 'post'));
                }
                
                $brand_model->create($data);
                self::clear_cache();
                $this->prompt('success', '添加品牌成功', url($this->MOD.'/brand', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->tpl_display('goods/brand.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'brand_name' => trim(vds_request('brand_name', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );
            
            $brand_model = new brand_model();
            $verifier = $brand_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($_FILES['logo_file']['name']))
                {
                    $save_path = 'upload'.DS.'brand'.DS.'logo'.DS;
                    $uploader = new uploader($save_path);
                    $logo = $uploader->upload_file('logo_file');
                    if ($logo['error'] == 'success') $data['brand_logo'] = $logo['url'];
                    else $this->prompt('error', $logo['error']);
                }
                else
                {
                    $data['brand_logo'] = trim(vds_request('logo_src', '', 'post'));
                }
                
                if($brand_model->update(array('brand_id' => vds_request('id')), $data) > 0)
                {
                    self::clear_cache();
                    $this->prompt('success', '更新品牌成功', url($this->MOD.'/brand', 'index'));
                } 
                else
                {
                    $this->prompt('error', '更新品牌失败');
                }    
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $brand_model = new brand_model();
            if($this->rs = $brand_model->find(array('brand_id' => vds_request('id'))))
            {
                $this->tpl_display('goods/brand.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }

    public function action_delete()
    {
        $condition = array('brand_id' => vds_request('id'));
        $brand_model = new brand_model();
        if($brand_model->delete($condition) > 0)
        {
            $cate_brand_model = new goods_cate_brand_model();
            $cate_brand_model->delete($condition);
            self::clear_cache();
            $this->prompt('success', '删除品牌成功', url($this->MOD.'/brand', 'index'));
        }  
        else
        {
            $this->prompt('error', '删除品牌失败');
        }    
    }
    
    //清除缓存
    private static function clear_cache()
    {
        $vcache = new vcache();
        $vcache->brand_model('indexed_list', null, -1);
    }
}