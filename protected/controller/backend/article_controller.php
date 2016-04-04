<?php
class article_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $cate_id = vds_request('cate_id', '', 'post');
            $status = vds_request('status', '', 'post');
            $kw = vds_request('kw', '', 'post');
            
            $where = 'WHERE 1';
            $binds = array();
            
            if($cate_id != '')
            {
                $where .= ' AND cate_id = :cate_id';
                $binds[':cate_id'] = $cate_id;
            }
            if($status != '')
            {
                $where .= ' AND status = :status';
                $binds[':status'] = $status;
            }
            if(!empty($kw))
            {
                $where .= ' AND title LIKE :kw';
                $binds[':kw'] = '%'.$kw.'%';
            }
            
            $article_model = new article_model();
            $total = $article_model->query("SELECT COUNT(*) as count FROM {$article_model->table_name} {$where}", $binds);
            if($total[0]['count'] > 0)
            {
                $article_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
                $limit = $article_model->pager_section();
                
                $sort_id = vds_request('sort_id', 0, 'post');
                $sortmap = array('id DESC', 'created_date ASC', 'created_date DESC');
                $sort = isset($sortmap[$sort_id])? $sortmap[$sort_id] : $sortmap[0];
                
                $sql = "SELECT id, cate_id, title, created_date, status
                        FROM {$article_model->table_name} {$where}
                        ORDER BY {$sort} {$limit}
                       ";
                
                $results = array
                (
                    'status' => 1,
                    'article_list' => $article_model->query($sql, $binds),
                    'paging' => $article_model->page
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
            $this->cateselect = $GLOBALS['instance']['cache']->article_cate_model('indexed_list');
            $this->tpl_display('article/article_list.html');
        }
    }
    
    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'title' => trim(vds_request('title', '', 'post')),
                'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                'brief' => trim(vds_request('brief', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'content' => stripslashes(vds_request('content', '', 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'status' => intval(vds_request('status', 0, 'post')),
                'created_date' => time(),
            );
                
            $article_model = new article_model();
            $verifier = $article_model->verifier($data);
            if(TRUE === $verifier)
            {
                //上传图片
                if(!empty($_FILES['picture_file']['name']))
                {
                    $save_path = 'upload'.DS.'article'.DS.'image'.DS;
                    $uploader = new uploader($save_path);
                    $picture = $uploader->upload_file('picture_file');
                    if ($picture['error'] == 'success') $data['picture'] = $picture['url'];
                    else $this->prompt('error', $picture['error']);  
                }
                else
                {
                    $data['picture'] = trim(vds_request('picture_src', '', 'post'));
                }
                    
                $article_model->create($data);
                $this->prompt('success', '添加资讯成功', url($this->MOD.'/article', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->cateselect = $GLOBALS['instance']['cache']->article_cate_model('indexed_list');
            $this->tpl_display('article/article.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $data = array
            (
                'title' => trim(vds_request('title', '', 'post')),
                'cate_id' => intval(vds_request('cate_id', 0, 'post')),
                'brief' => trim(vds_request('brief', '', 'post')),
                'link' => trim(vds_request('link', '', 'post')),
                'content' => stripslashes(vds_request('content', '', 'post')),
                'meta_keywords' => trim(vds_request('meta_keywords', '', 'post')),
                'meta_description' => trim(vds_request('meta_description', '', 'post')),
                'status' => intval(vds_request('status', 0, 'post')),
            );
            
            $article_model = new article_model();
            $verifier = $article_model->verifier($data);
            if(TRUE === $verifier)
            {
                //更新图片
                if(!empty($_FILES['picture_file']['name']))
                {
                    $save_path = 'upload'.DS.'article'.DS.'image'.DS;
                    $uploader = new uploader($save_path);
                    $picture = $uploader->upload_file('picture_file');
                    if($picture['error'] == 'success') $data['picture'] = $picture['url'];
                    else $this->prompt('error', $picture['error']);
                }
                else
                {
                    $data['picture'] = trim(vds_request('picture_src', '', 'post'));
                }
                    
                if($article_model->update(array('id' => vds_request('id')), $data) > 0)
                    $this->prompt('success', '更新资讯成功', url($this->MOD.'/article', 'index'));
                else
                    $this->prompt('error', '更新资讯失败');
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $id = vds_request('id');
            $article_model = new article_model();
            if($this->rs = $article_model->find(array('id' => $id)))
            {
                $this->cateselect = $GLOBALS['instance']['cache']->article_cate_model('indexed_list');
                $this->tpl_display('article/article.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_editor()
    {
        $save_path = 'upload'.DS.'article'.DS.'editor'.DS;
        $uploader = new uploader($save_path);
        $file = $uploader->upload_file('upfile');
        if($file['error'] == 'success')
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
        if(is_array($id) && !empty($id))
        {
            $affected = 0;
            $article_model = new article_model();
            foreach($id as $v) $affected += $article_model->delete(array('id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个资讯记录, 失败 {$failure} 个", url($this->MOD.'/article', 'index'));
        }
        else
        {
            $this->prompt('error', '参数错误');
        }
    }
}