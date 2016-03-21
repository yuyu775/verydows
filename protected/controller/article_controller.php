<?php
class article_controller extends general_controller
{
	public function action_index()
    {
        $id = vds_request('id', null, 'get');
        $article_model = new article_model();
        if($this->article = $article_model->find(array('id' => $id)))
        {
            $vcache = new vcache();
            $this->article_cate_list = $vcache->article_cate_model('indexed_list');
            
            $this->tpl_display('article.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_list()
    {
        $vcache = new vcache();
        $this->article_cate_list = $vcache->article_cate_model('indexed_list');
        $article_model = new article_model();
        $this->article_list = $article_model->find_all(null, 'created_date DESC', 'id, title, link, created_date', array(vds_request('page', 1), 20));
        $this->article_paging = $article_model->page;
        $this->tpl_display('article_list.html');
    }
    
    public function action_category()
    {
        $cate_id = vds_request('id', null, 'get');
        $article_cate_model = new article_cate_model();
        if($this->cate = $article_cate_model->find(array('cate_id' => $cate_id)))
        {
            $article_model = new article_model();
            $this->article_list = $article_model->find_all(array('cate_id' => $cate_id), 'created_date DESC', 'id, title, link, created_date', array(vds_request('page', 1), 20));
            $this->article_paging = $article_model->page;
            $vcache = new vcache();
            $this->article_cate_list = $vcache->article_cate_model('indexed_list');
            $this->tpl_display('article_category.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
}