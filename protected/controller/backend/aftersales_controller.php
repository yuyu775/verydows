<?php
class aftersales_controller extends general_controller
{
	public function action_index()
    {
        $aftersales_model = new aftersales_model();
        if(vds_request('step') == 'search')
        {
            $where = ' WHERE 1';
            $conditions = $binds = array();
            
            $type = vds_request('type', '');
            if($type != '')
            {
                $where .= " AND a.type = :type";
                $conditions['type'] = $binds[':type'] = $type;
            }
            
            $status = vds_request('status', '', 'post');
            if($status != '')
            {
                $where .= " AND a.status = :status";
                $conditions['status'] = $binds[':status'] = $status;
            }
            
            $order_id = vds_request('order_id', '', 'post');
            if($order_id != '')
            {
                $where .= " AND a.order_id = :order_id";
                $conditions['order_id'] = $binds[':order_id'] = $order_id;
            }
            
            $total = $aftersales_model->find_count($conditions);
            if($total > 0)
            {
                $sort_id = vds_request('sort_id', 0, 'post');
                $sort_map = array('as_id DESC', 'created_date ASC', 'created_date DESC');
                $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                
                $aftersales_model->pager(vds_request('page', 1), 15, 10, $total);
                $limit = $aftersales_model->pager_section();
                
                $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
                $sql = "SELECT a.as_id, a.order_id, a.type, a.goods_qty, a.created_date, a.status,
                               b.goods_id, b.goods_name, b.goods_opts,
                               c.user_id, c.username
                        FROM {$aftersales_model->table_name} AS a
                        LEFT JOIN {$tblpre}order_goods AS b
                        ON a.order_id = b.order_id AND a.goods_id = b.goods_id
                        LEFT JOIN {$tblpre}user AS c
                        ON a.user_id = c.user_id
                        {$where} ORDER BY {$sort} {$limit}
                       ";
                
                $list = $aftersales_model->query($sql, $binds);
                foreach($list as $k => $v)
                {
                    if(!empty($v['goods_opts'])) $list[$k]['goods_opts'] = json_decode($v['goods_opts'], TRUE);
                }
                 
                $results = array
                (
                    'status' => 1,
                    'list' => $list,
                    'paging' => $aftersales_model->page,
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
            $this->type_map = $aftersales_model->type_map;
            $this->status_map = $aftersales_model->status_map;
            $this->tpl_display('operation/aftersales_list.html');
        }
	}
    
    public function action_view()
    {
        $id = vds_request('id', null, 'get');
        $aftersales_model = new aftersales_model();
        $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
        $sql = "SELECT a.*, b.goods_name, b.goods_opts, c.username, c.email
                FROM {$aftersales_model->table_name} AS a
                LEFT JOIN {$tblpre}order_goods AS b
                ON a.order_id = b.order_id AND a.goods_id = b.goods_id
                LEFT JOIN {$tblpre}user AS c
                ON a.user_id = c.user_id
                WHERE a.as_id = :id
                LIMIT 1
               ";
        if($row = $aftersales_model->query($sql, array(':id' => $id)))
        {
            $row[0]['goods_opts'] = !empty($row[0]['goods_opts']) ?  json_decode($row[0]['goods_opts'], TRUE) : array();
            $this->rs = $row[0];
            $this->type_map = $aftersales_model->type_map;
            $this->status_map = $aftersales_model->status_map;
            $message_model = new aftersales_message_model();
            $this->message_list = $message_model->find_all(array('as_id' => $id), 'dateline ASC');
            $this->admin_list = $GLOBALS['instance']['cache']->admin_model('indexed_list');
            
            $this->tpl_display('operation/aftersales.html');
        }
        else
        {
            $this->prompt('error', '未找到相应的数据记录');
        }
    }
    
    public function action_status()
    {
        $as_id = vds_request('id');
        $status = intval(vds_request('status', 0, 'post'));
        $aftersales_model = new aftersales_model();
        if($aftersales_model->update(array('as_id' => $as_id), array('status' => $status)) > 0)
        {
           $this->prompt('success', '操作成功', url($this->MOD.'/aftersales', 'view', array('id' => $as_id)));
        }
        else
        {
            $this->prompt('success', '操作失败');
        }
    }
    
    public function action_reply()
    {
        $data = array
        (
            'as_id' => vds_request('as_id'),
            'admin_id' => $_SESSION['admin']['user_id'],
            'content' => strip_tags(trim(vds_request('content', '', 'post'))),
            'dateline' => $_SERVER['REQUEST_TIME'],
        );
            
        $message_model = new aftersales_message_model();
        $verifier = $message_model->verifier($data);
        if(TRUE === $verifier)
        {
            $message_model->create($data);
            $this->prompt('success', '回复消息成功');
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_delete()
    {
        if(vds_request('step') == 'message')
        {
            $id = vds_request('id');
            if(!empty($id) && is_array($id))
            {
                $message_model = new aftersales_message_model();
                $affected = 0;
                foreach($id as $v) if($message_model->delete(array('id' => $v)) > 0) $affected += 1; 
                $failure = count($id) - $affected;
                $this->prompt('default', "成功删除 {$affected} 个消息记录, 失败 {$failure} 个");
            }
            else
            {
                $this->prompt('error', '参数错误');
            }
        }
        else
        {
            $id = vds_request('id');
            $aftersales_model = new aftersales_model();
            $message_model = new aftersales_message_model();
            if(is_array($id))
            {
                $affected = 0;
                foreach($id as $v) 
                {
                    $condition = array('as_id' => $v);
                    if($aftersales_model->delete($condition) > 0)
                    {
                        $affected += 1;
                        $message_model->delete($condition);
                    }
                }
                $failure = count($id) - $affected;
                $this->prompt('default', "成功删除 {$affected} 个售后服务记录, 失败 {$failure} 个", url($this->MOD.'/aftersales', 'index'));
            }
            else
            {
                $condition = array('as_id' => $id);
                if($aftersales_model->delete($condition) > 0)
                {
                    $message_model->delete($condition);
                    $this->prompt('success', '删除成功', url($this->MOD.'/aftersales', 'index'));
                }
                else
                {
                    $this->prompt('error', '删除失败');
                }
            }
        }
    }
}