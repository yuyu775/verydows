<?php
class adv_controller extends general_controller
{
    public function action_index()
    {
        $where = 'WHERE 1';
        $binds = array();
            
        if($start_date = strtotime(vds_request('start_date', '', 'post'))) $where .= " AND start_date >= {$start_date}";
        if($end_date = strtotime(vds_request('end_date', '', 'post'))) $where .= " AND end_date <= {$end_date}";
            
        $type = vds_request('type', '', 'post');
        if($type != '')
        {
            $where .= " AND type = :type";
            $binds[':type'] = $type;
        }
            
        $status = vds_request('status', '', 'post');
        if($status != '')
        {
            $where .= " AND status = :status";
            $binds[':status'] = $status;
        }
            
        $kw = trim(vds_request('kw', '', 'post'));
        if($kw != '')
        {
            $where .= ' AND name LIKE :kw';
            $binds[':kw'] = '%'.$kw.'%';
        }
            
        $adv_model = new adv_model();
        $total = $adv_model->query("SELECT COUNT(*) as count FROM {$adv_model->table_name} {$where}", $binds);
        $adv_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
        $limit = $adv_model->pager_section();
        $sql = "SELECT adv_id, position_id, name, type, start_date, end_date, seq, status
                FROM {$adv_model->table_name} {$where}
                ORDER BY adv_id DESC {$limit}
               ";
        $this->results = $adv_model->query($sql ,$binds);
        $this->paging = $adv_model->page;
        $this->type_map = $adv_model->type_map;
        $this->position_list = $GLOBALS['instance']['cache']->adv_position_model('indexed_list');
        $this->tpl_display('adv/adv_list.html');
    }

    public function action_add()
    {
        if(vds_request('step') == 'submit')
        {
            $type = vds_request('type', '', 'post');
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'position_id' => vds_request('position_id', 0, 'post'),
                'type' => $type,
                'start_date' => trim(vds_request('start_date', 0, 'post')),
                'end_date' => trim(vds_request('end_date', 0, 'post')),
                'seq' => trim(vds_request('seq', 99, 'post')),
                'status' => intval(vds_request('status', 0, 'post')),
            );
            
            $adv_model = new adv_model();
            $rule_slices = array('width' => FALSE, 'height' => FALSE, 'link' => FALSE, 'content' => FALSE);
            $verifier = $adv_model->verifier($data, $rule_slices);
            if(TRUE === $verifier)
            {
                $data['start_date'] = !empty($data['start_date']) ? strtotime($data['start_date']) : 0;
                $data['end_date'] = !empty($data['end_date']) ? strtotime($data['end_date']) : 0;
                $params = vds_request($type.'_params', null, 'post');
                $processed = $this->process_params($type, $params);
                $data = $data + $processed;
                $adv_model->create($data);
                $this->prompt('success', '添加广告成功', url($this->MOD.'/adv', 'index'));
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $this->position_list = $GLOBALS['instance']['cache']->adv_position_model('indexed_list');
            $this->position_id = vds_request('position', 0, 'get');
            $this->tpl_display('adv/adv.html');
        }
    }
    
    public function action_edit()
    {
        if(vds_request('step') == 'submit')
        {
            $type = vds_request('type', null, 'post');
            $data = array
            (
                'name' => trim(vds_request('name', '', 'post')),
                'position_id' => vds_request('position_id', 0, 'post'),
                'type' => $type,
                'start_date' => trim(vds_request('start_date', 0, 'post')),
                'end_date' => trim(vds_request('end_date', 0, 'post')),
                'seq' => trim(vds_request('seq', 99, 'post')),
                'status' => intval(vds_request('status', 0, 'post')),
            );
            
            $adv_model = new adv_model();
            $rule_slices = array('width' => FALSE, 'height' => FALSE, 'link' => FALSE, 'content' => FALSE);
            $verifier = $adv_model->verifier($data, $rule_slices);
            if(TRUE === $verifier)
            {
                $data['start_date'] = !empty($data['start_date']) ? strtotime($data['start_date']) : 0;
                $data['end_date'] = !empty($data['end_date']) ? strtotime($data['end_date']) : 0;
                $params = vds_request($type.'_params', null, 'post');
                $processed = $this->process_params($type, $params);
                $data = $data + $processed;
                
                if($adv_model->update(array('adv_id' => vds_request('id')), $data) > 0) $this->prompt('success', '更新广告成功', url($this->MOD.'/adv', 'index'));
                $this->prompt('error', '更新广告失败');
            }
            else
            {
                $this->prompt('error', $verifier);
            }
        }
        else
        {
            $adv_model = new adv_model();
            if($rs = $adv_model->find(array('adv_id' => vds_request('id'))))
            {
                $rs['params'] = json_decode($rs['params'], TRUE);
                $this->rs = $rs;
                $this->position_list = $GLOBALS['instance']['cache']->adv_position_model('indexed_list');
                $this->tpl_display('adv/adv.html');
            }
            else
            {
                $this->prompt('error', '未找到相应的数据记录');
            }
        }
    }
    
    public function action_delete()
    {
        $id = vds_request('id', array());
        if(!empty($id))
        {
            $affected = 0;
            $adv_model = new adv_model();
            foreach($id as $v) $affected += $adv_model->delete(array('adv_id' => $v));
            $failure = count($id) - $affected;
            $this->prompt('default', "成功删除 {$affected} 个记录, 失败 {$failure} 个", url($this->MOD.'/adv', 'index'));
        }
        else
        {
            $this->prompt('error', '无法获取参数');
        }
    }
    
    //处理不同广告参数
    private function process_params($type, $params)
    {
        $adv_model = new adv_model();
        $rule_slices = array('name' => FALSE, 'start_date' => FALSE, 'end_date' => FALSE, 'seq' => FALSE, 'type' => FALSE);
        switch($type)
        {
            case 'image':
                $rule_slices['content'] = FALSE;
                $verifier = $adv_model->verifier($params, $rule_slices);
                if(TRUE === $verifier)
                {
                    if(!empty($_FILES['image_file']['name']))
                    {
                        $save_path = 'upload'.DS.'adv'.DS.'image'.DS;
                        $uploader = new uploader($save_path);
                        $image = $uploader->upload_file('image_file');
                        if($image['error'] == 'success') $params['src'] = $image['url'];
                        else $this->prompt('error', $image['error']);
                    }
                    if(!empty($params['src']))
                    {
                        $img_attr = "src=\"{$params['src']}\"";
                        if(!empty($params['width'])) $img_attr .= " width=\"{$params['width']}\"";
                        if(!empty($params['height'])) $img_attr .= " height=\"{$params['height']}\"";
                        if(!empty($params['title'])) $img_attr .= " alt=\"{$params['title']}\"";
                        $data['codes'] = "<a href=\"{$params['link']}\"><img {$img_attr} border=\"0\" /></a>";
                    }
                    else
                    {
                        $this->prompt('error', '请上传图片文件或输入文件URL');
                    }
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
                        
            break;
                    
            case 'flash':
                    
                $rule_slices['content'] = $rule_slices['link'] = FALSE;
                $verifier = $adv_model->verifier($params, $rule_slices);
                if(TRUE === $verifier)
                {
                    if(!empty($_FILES['flash_file']['name']))
                    {
                        $save_path = 'upload'.DS.'adv'.DS.'flash'.DS;
                        $uploader = new uploader($save_path, array('.swf', '.flv'));
                        $flash = $uploader->upload_file('flash_file');
                        if($flash['error'] == 'success') $params['src'] = $flash['url'];
                        else $this->prompt('error', $flash['error']);
                    }
                    if(!empty($params['src']))
                    {
                        $flash_attr = "src=\"{$params['src']}\"";
                        if(!empty($params['width'])) $flash_attr .= " width=\"{$params['width']}\"";
                        if(!empty($params['height'])) $flash_attr .= " height=\"{$params['height']}\"";
                        $data['codes'] = "<embed {$flash_attr} type=\"application/x-shockwave-flash\" wmode=\"transparent\"></embed>";
                    }
                    else
                    {
                        $this->prompt('error', '请上传Flash文件或输入文件URL');
                    }
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
                        
            break;
                    
            case 'text':
                
                $rule_slices['width'] = $rule_slices['height'] = FALSE;
                $verifier = $adv_model->verifier($params, $rule_slices);
                if(TRUE === $verifier)
                {
                    $stylestr = '';
                    foreach(explode(',', $params['style']) as $item)
                    {
                        if(!empty($item))
                        {
                            $k = strtok($item, ':'); $v = strtok(':');
                            if(!empty($v))
                            {
                                switch($k)
                                {
                                    case 'c': $stylestr .= "color:{$v};"; break;
                                    case 's': $stylestr .= "font-size:{$v};"; break;
                                    case 'b': $stylestr .= "font-weight:bold;"; break;
                                    case 'u': $stylestr .= "text-decoration:underline;"; break;
                                    case 'i': $stylestr .= "font-style:italic;"; break;
                                }
                            }
                            $stylearr[$k] = $v;
                        }
                    }
                    $params['style'] = $stylearr;
                    if($stylestr != '') $stylestr = " style=\"{$stylestr}\"";
                    $data['codes'] = "<a href=\"{$params['link']}\"{$stylestr}>{$params['content']}</a>";
                }
                else
                {
                    $this->prompt('error', $verifier);
                }  
                        
            break;
                    
            case 'code':
                    
                $rule_slices['width'] = $rule_slices['height'] = $rule_slices['link'] = FALSE;
                $verifier = $adv_model->verifier($params, $rule_slices);
                if(TRUE === $verifier)
                {
                    $data['codes'] = stripslashes($params['content']);
                    $params = array();
                }
                else
                {
                    $this->prompt('error', $verifier);
                }
                        
            break;
        }
        
        $data['params'] = json_encode($params);
        return $data;
    }
    
}