<?php
class user_controller extends general_controller
{
    public function action_index()
    {
        if(vds_request('step') == 'search')
        {
            $where = 'WHERE 1';
            $binds = array();
            $kw = vds_request('kw', '', 'post');
            if($kw != '')
            {
                $where_field = vds_request('type', 0, 'post') == 0 ? 'username' : 'email';
                $where .= " AND {$where_field} LIKE :kw";
                $binds[':kw'] = '%'.$kw.'%';
            }
            
            $user_model = new user_model();
            $total = $user_model->query("SELECT COUNT(*) as count FROM {$user_model->table_name} {$where}", $binds);
            if($total[0]['count'] > 0)
            {
                $pager = $user_model->pager(vds_request('page', 1), 15, 10, $total[0]['count']);
                $limit = $user_model->pager_section();
                
                $sql = "SELECT a.user_id, a.username, a.email, a.status, a.email_status,
                               b.created_date, b.last_date
                        FROM {$user_model->table_name} AS a
                        LEFT JOIN {$GLOBALS['mysql']['MYSQL_DB_TABLE_PRE']}user_actinfo AS b
                        ON a.user_id = b.user_id
                        {$where}
                        ORDER BY a.user_id DESC {$limit}
                       ";
                
                $results = array
                (
                    'status' => 1,
                    'list' => $user_model->query($sql, $binds),
                    'paging' => $user_model->page,
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
            $this->tpl_display('user/user_list.html');
        }
    }

    public function action_view()
    {
        $user_id = vds_request('id');
        $condition = array('user_id' => $user_id);
        $user_model = new user_model();
        if($user = $user_model->find($condition))
        {
            switch(vds_request('step'))
            {
                case 'order':
    
                    $order_status = vds_request('order_status', '', 'post');
                    if($order_status != '') $condition['order_status'] = $order_status;
                    
                    $order_id = vds_request('order_id', '', 'post');
                    if($order_id != '') $condition['order_id'] = $order_id;
                    
                    $sort_id = vds_request('sort_id', 0, 'post');
                    $sort_map = array('created_date DESC', 'created_date ASC', 'order_amount DESC', 'order_amount ASC');
                    $sort = isset($sort_map[$sort_id])? $sort_map[$sort_id] : $sort_map[0];
                    
                    $order_model = new order_model();
                    $status_map = $order_model->status_map;
                    if($order_list = $order_model->find_all($condition, $sort, '*', array(vds_request('page', 1, 'get'), 15)))
                    {
                        foreach($order_list as $k => $v)
                        {
                            $order_list[$k]['consignee'] = json_decode($v['consignee'], TRUE);
                            $order_list[$k]['order_status'] = $status_map[$v['order_status']];
                        }
                        $this->order_list = $order_list;
                        $this->order_paging = $order_model->page;
                    }
                    
                    $this->user = $user;
                    $this->status_map = $status_map;
                    
                    $this->tpl_display('user/order_list.html');
                
                break;
                
                case 'consignee':
                
                    $consignee_model = new user_consignee_model();
                    $this->consignee_list = $consignee_model->get_user_consignee_list($user_id);
                    $this->user = $user;
                    $this->tpl_display('user/consignee_list.html');
                
                break;
                
                case 'account':
   
                    $log_model = new user_account_log_model();
                    if($this->log_list = $log_model->find_all($condition, 'dateline DESC', '*', array(vds_request('page', 1), 15)))
                    {
                        $this->log_paging = $log_model->page;
                        $vcache = new vcache();
                        $this->admin_list = $vcache->admin_model('indexed_list');
                    }
                    $account_model = new user_account_model();
                    $user['account'] = $account_model->find($condition);
                    $this->user = $user;
                    
                    $this->tpl_display('user/user_account_log_list.html');
                    
                break;
                
                default:
                
                    include(VIEW_DIR.DS.'function'.DS.'html_date_options.php');
                    //活动信息
                    $actinfo_model = new user_actinfo_model();
                    $user['actinfo'] = $actinfo_model->find($condition);
                    //资料信息
                    $profile_model = new user_profile_model();
                    $user['profile'] = $profile_model->find($condition);
                    //账户信息
                    $account_model = new user_account_model();
                    $user['account'] = $account_model->find($condition);
                    //用户组信息
                    $group_model = new user_group_model();
                    $user['group'] = $group_model->get_user_group($user['account']['exp']);
                    $group_max_exp = $group_model->query("SELECT MAX(min_exp) AS max FROM {$group_model->table_name}");
                    $user['account']['exp_pct'] = round(($user['account']['exp']/$group_max_exp[0]['max'])*100) . '%';
                    $this->rs = $user;
                    $this->tpl_display('user/details.html');
            }
        }
        else
        {
            $this->prompt('error', '无法找到相应的用户记录');
        }
    }
    
    public function action_revise_account()
    {
        $user_id = vds_request('id', 0, 'get');
        $data = array
        (
            'balance' => floatval(vds_request('balance', 0, 'post')),
            'points' => intval(vds_request('points', 0, 'post')),
            'exp' => intval(vds_request('exp', 0, 'post')),
            'cause' => trim(vds_request('cause', '', 'post')),
        );
        if(empty($data['balance']) && empty($data['points']) && empty($data['exp'])) $this->prompt('error', '经验、余额或积分至少输入一项');
        if($data['cause'] == '') $this->prompt('error', '原因/备注不能为空');
          
        $account_model = new user_account_model();
        $verifier = $account_model->verifier($data);
        if(TRUE === $verifier)
        {
            $sym_balance = vds_request('sym_balance', 1, 'post');
            $sym_points = vds_request('sym_points', 1, 'post');
            $sym_exp = vds_request('sym_exp', 1, 'post');
            if($sym_balance == -1) $data['balance'] = 0 - $data['balance'];
            if($sym_points == -1) $data['points'] = 0 - $data['points'];
            if($sym_exp == -1) $data['exp'] = 0 - $data['exp'];
             
            $sql = "UPDATE {$account_model->table_name}
                    SET balance = balance + :balance, points = points + :points, exp = exp + :exp
                    WHERE user_id = :user_id
                   ";
            $binds = array(':balance' => $data['balance'], ':points' => $data['points'], ':exp' => $data['exp'], ':user_id' => $user_id);
            if($account_model->execute($sql, $binds) > 0)
            {
                $data['user_id'] = $user_id;
                $data['admin_id'] = $_SESSION['admin']['user_id'];
                $data['dateline'] = $_SERVER['REQUEST_TIME'];
                $log_model = new user_account_log_model();
                $log_model->create($data);
                
                $this->prompt('success', '调整用户账户数据成功', url($this->MOD.'/user', 'view', array('id' => $user_id)));
            }    
            else
            {
                $this->prompt('error', '调整用户账户数据失败');
            }       
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_reset_password()
    {
        $data = array
        (
            'password' => trim(vds_request('password', '', 'post')),
            'repassword' => trim(vds_request('repassword', '', 'post')),
        );
        
        $user_model = new user_model();
        $verifier = $user_model->verifier($data, array('username' => FALSE, 'email' => FALSE));
        if(TRUE === $verifier)
        {
            $condition = array('user_id' => vds_request('id'));
            $data['password'] = md5($data['password']);
            unset($data['repassword']);
            $user_model->update($condition, $data);
            $this->prompt('success', '修改用户密码成功');
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_edit_profile()
    {
        $data = array
        (
            'name' => trim(vds_request('name', '', 'post')),
            'gender' => intval(vds_request('gender', 0, 'post')),
            'birth_year' => intval(vds_request('birth_year', 0, 'post')),
            'birth_month' => intval(vds_request('birth_month', 0, 'post')),
            'birth_day' => intval(vds_request('birth_day', 0, 'post')),
            'mobile_no' => trim(vds_request('mobile_no', '', 'post')),
            'qq' => trim(vds_request('qq', '', 'post')),
            'signature' => trim(vds_request('signature', '', 'post')),
        );
        
        $profile_model = new user_profile_model();
        $verifier = $profile_model->verifier($data);
        if(TRUE === $verifier)
        {
            $user_id = vds_request('id');
            //上传头像图片
            if(!empty($_FILES['avatar']['name']))
            {
                $save_path = 'upload'.DS.'user'.DS.'avatar'.DS;
                $save_name = vfunc::random_chars(10).uniqid($user_id);
                
                $uploader = new uploader($save_path);
                $avatar = $uploader->upload_file('avatar', $save_name);
                if ($avatar['error'] == 'success') $data['avatar'] = $avatar['name'];
                else $this->prompt('error', $avatar['error']);
            }
            
            if($profile_model->update(array('user_id' => $user_id), $data) > 0) $this->prompt('success', '更新用户资料成功');
            $this->prompt('error', '更新用户资料失败');
        }
        else
        {
            $this->prompt('error', $verifier);
        }
    }
    
    public function action_delete()
    {
        $condition = array('user_id' => vds_request('id'));
        $user_model = new user_model();
        if($user_model->delete($condition) > 0)
        {
            //删除用户资料
            $profile_model = new user_profile_model();
            $profile_model->delete($condition);
            //删除用户账户
            $account_model = new user_account_model();
            $account_model->delete($condition);
            //删除用户收件人
            $consignee_model = new user_consignee_model();
            $consignee_model->delete($condition);
            //删除用户收藏
            $favorite_model = new user_favorite_model();
            $favorite_model->delete($condition);
            //删除商品评价
            $review_model = new goods_review_model();
            $review_model->delete($condition);
            //删除售后记录
            $aftersales_model = new aftersales_model();
            $aftersales_model->delete($condition);
            
            $this->prompt('success', '删除用户成功', url($this->MOD.'/user', 'index'));
        }  
        else
        {
            $this->prompt('error', '删除用户失败');
        }   
    }

}