<?php
class goods_cate_attr_controller extends general_controller
{
    public function action_index()
    {
        $cate_id = vds_request('cate_id');
        $cate_model = new goods_cate_model();
        if($this->cate = $cate_model->find(array('cate_id' => $cate_id)))
        {
            $attr_model = new goods_cate_attr_model();
            $attrs = $attr_model->find_all(array('cate_id' => $cate_id), 'seq ASC');
            foreach($attrs as $k => $v) if(!empty($v['opts'])) $attrs[$k]['opts'] = json_decode($v['opts'], TRUE);
            $this->attrs = $attrs;
            $this->tpl_display('goods/cate_attr_list.html');
        }
        else
        {
            $this->prompt('error', '未找到相应的数据记录');
        }
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $cate_id = intval(vds_request('cate_id'));
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'cate_id' => $cate_id,
                'opts' => vds_request('opts', '', 'post'),
                'filtrate' => intval(vds_request('filtrate', 0, 'post')),
                'uom' => trim(vds_request('uom', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );
                    
            $attr_model = new goods_cate_attr_model();
            $verifier = $attr_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($data['opts'])) $data['opts'] = json_encode($data['opts']);
                $attr_model->create($data);
                $this->prompt('success', '添加分类属性成功', url($this->MOD.'/goods_cate_attr', 'index', array('cate_id' => $cate_id)));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->cate_id = vds_request('cate_id'); 
            $this->tpl_display('goods/cate_attr.html');
        }
    }

    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $cate_id = intval(vds_request('cate_id'));
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'cate_id' => $cate_id,
                'opts' => vds_request('opts', '', 'post'),
                'filtrate' => intval(vds_request('filtrate', 0, 'post')),
                'uom' => trim(vds_request('uom', '', 'post')),
                'seq' => intval(vds_request('seq', 99, 'post')),
            );
            
            $attr_model = new goods_cate_attr_model();
            $verifier = $attr_model->verifier($data);
            if(TRUE === $verifier)
            {
                if(!empty($data['opts'])) $data['opts'] = json_encode($data['opts']);
                if($attr_model->update(array('attr_id' => vds_request('id')), $data) > 0)
                {
                    $this->prompt('success', '更新分类属性成功', url($this->MOD.'/goods_cate_attr', 'index', array('cate_id' => $cate_id)));
                }  
                else
                {
                    $this->prompt('error', '更新分类属性失败');
                } 
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $attr_model = new goods_cate_attr_model();
            if($rs = $attr_model->find(array('attr_id' => vds_request('id'))))
            {
                if(!empty($rs['opts'])) $rs['opts'] = json_decode($rs['opts'], TRUE);
                $this->rs = $rs;
                $this->tpl_display('goods/cate_attr.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_delete()
    {
        $condition = array('attr_id' => vds_request('id'));
        $attr_model = new goods_cate_attr_model();
        if($attr_model->delete($condition) > 0)
        {
            $goods_attr_model = new goods_attr_model();
            $goods_attr_model->delete($condition);
            $this->prompt('success', '删除分类属性成功');
        }  
        else
        {
            $this->prompt('error', '删除分类属性');
        }  
    }
}