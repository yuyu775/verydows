<?php
class category_controller extends general_controller
{
    public function action_index()
    {
        $id = intval(vds_request('id', 0, 'get'));
        $cate_model = new goods_cate_model();
        if($this->cateinfo = $cate_model->find(array('cate_id' => $id)))
        {
            $this->breadcrumbs = $cate_model->breadcrumbs($id);
            $this->recommend = $GLOBALS['instance']['cache']->goods_model('find_goods', array(array('cate' => $id, 'recommend' => 1), 5), $GLOBALS['cfg']['data_cache_lifetime']);
            $this->bargain = $GLOBALS['instance']['cache']->goods_model('find_goods', array(array('cate' => $id, 'bargain' => 1), 5), $GLOBALS['cfg']['data_cache_lifetime']);
            
            $conditions = array
            (
                'cate' => $id,
                'brand' => vds_request('brand', '', 'get'), 
                'att' => vds_request('att', '', 'get'),
                'minpri' => intval(vds_request('minpri', 0, 'get')),
                'maxpri' => intval(vds_request('maxpri', 0, 'get')),
                'sort' => intval(vds_request('sort', 0, 'get')),
                'page' => intval(vds_request('page', 1, 'get')),
            );
            
            $this->filters = $cate_model->set_filters($id, $conditions['att']);

            $goods_model = new goods_model();
            $this->history = $goods_model->get_history();
            $this->goods_list = $goods_model->find_goods($conditions, array(vds_request('page', 1), $GLOBALS['cfg']['cate_goods_per_num']));
            $this->goods_paging = $goods_model->page;
            $this->u = $conditions;
            
            $this->tpl_display('category.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }

}
