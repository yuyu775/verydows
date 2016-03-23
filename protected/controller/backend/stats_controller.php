<?php
class stats_controller extends general_controller
{
    public function action_order()
    {
        if(vds_request('step') == 'query')
        {
            $start_year = vds_request('start_year', null, 'post');
            if(empty($start_year)) $start_year = date('Y'); 
            $start_timestamp = strtotime($start_year.'0101');
            $next_timestamp = strtotime($start_year + 1 .'0101');
            
            $order_model = new order_model();
            $sql = "SELECT COUNT(*) AS num, FROM_UNIXTIME(created_date, '%m') AS month 
                    FROM {$order_model->table_name}
                    WHERE created_date >= {$start_timestamp} && created_date < {$next_timestamp}
                    GROUP BY month
                   ";
            if($stats_data = $order_model->query($sql)) $results = array('status' => 1, 'data' => $stats_data);
            else $results = array('status' => 0);    
            echo json_encode($results);
        }
        else
        {
            include(VIEW_DIR.DS.'function'.DS.'html_date_options.php');
            $today_stamp = strtotime('today');
            $yesterday_stamp = strtotime('yesterday');
            $this_month_stamp = strtotime(date('Ym').'01');
            $order_model = new order_model();
            $sql = "SELECT COUNT(*) AS total, 
                    SUM(CASE WHEN order_status = 2 then 1 else 0 end) AS paid,
                    SUM(CASE WHEN order_status = 1 then 1 else 0 end) AS nonpay,
                    SUM(CASE WHEN order_status = 0 then 1 else 0 end) AS canceled
                    FROM {$order_model->table_name}
                    WHERE
                   ";
            $today = $order_model->query($sql." created_date >= {$today_stamp}");
            $yesterday = $order_model->query($sql." created_date >= {$yesterday_stamp} && created_date < {$today_stamp}");
            $this_month = $order_model->query($sql." created_date >= {$this_month_stamp} && created_date < {$today_stamp}");
            $this->latest = array
            (
                '今日' => $today[0],
                '昨日' => $yesterday[0],
                '本月' => $this_month[0],
            );
            $this->def_year = date('Y');
            $this->tpl_display('operation/stats_order.html');
        }
    }
    
    public function action_revenue()
    {
        if(vds_request('step') == 'query')
        {
            $start_year = vds_request('start_year', null, 'post');
            if(empty($start_year)) $start_year = date('Y'); 
            $start_timestamp = strtotime($start_year.'0101');
            $next_timestamp = strtotime($start_year + 1 .'0101');
            
            $order_model = new order_model();
            $sql = "SELECT SUM(order_amount) AS revenue, FROM_UNIXTIME(created_date, '%m') AS month 
                    FROM {$order_model->table_name}
                    WHERE order_status >= 2 AND created_date >= {$start_timestamp} AND created_date < {$next_timestamp}
                    GROUP BY month
                   ";
            if($stats_data = $order_model->query($sql)) $results = array('status' => 1, 'data' => $stats_data);
            else $results = array('status' => 0);    
            echo json_encode($results);
        }
        else
        {
            include(VIEW_DIR.DS.'function'.DS.'html_date_options.php');
            $today_stamp = strtotime('today');
            $yesterday_stamp = strtotime('yesterday');
            $this_month_stamp = strtotime(date('Ym').'01');
            $order_model = new order_model();
            $sql = "SELECT SUM(order_amount) AS revenue
                    FROM {$order_model->table_name}
                    WHERE order_status >= 2
                   ";
            $today = $order_model->query($sql." AND created_date >= {$today_stamp}");
            $yesterday = $order_model->query($sql." AND created_date >= {$yesterday_stamp} AND created_date < {$today_stamp}");
            $this_month = $order_model->query($sql." AND created_date >= {$this_month_stamp} AND created_date < {$today_stamp}");
            $this->latest = array
            (
                '今日' => $today[0],
                '昨日' => $yesterday[0],
                '本月' => $this_month[0],
            );
            $this->def_year = date('Y');
            $this->tpl_display('operation/stats_revenue.html');
        }
    }
    
    public function action_visitor()
    {
        $todaystamp = strtotime('today');
        $this->todaystamp = $todaystamp;
        switch(vds_request('col'))
        {
            case 'referrer':
                
                if(vds_request('query') == 'async')
                {
                    $start_date = strtotime(vds_request('start_date', '', 'post'));
                    $end_date = strtotime(vds_request('end_date', date('Ymd'), 'post'));
                    $results = array('status' => 0);
                    if($start_date != FALSE && $end_date != FALSE && $end_date > $start_date)
                    {
                        $type = vds_request('type', '', 'post');
                        $stats_model = new visitor_stats_model();
                        if($stats_data = $stats_model->stats_period_referrer($type, $start_date, $end_date))
                        {
                            $results = array
                            (
                                'status' => 1,
                                'data' => $stats_data,
                            );
                        }
                    }
                    echo json_encode($results);
                }
                else
                {
                    $stats_model = new visitor_stats_model();
                    $this->latest = array
                    (
                        '今日' => $stats_model->stats_period_referrer('cate', $todaystamp, $todaystamp),
                        '昨日' => $stats_model->stats_period_referrer('cate', $todaystamp - 86400, $todaystamp - 86400),
                    );
                    $this->tpl_display('operation'.DS.'stats_referrer.html');
                }
                
            
            break;
            
            case 'terminal':
                 
                if(vds_request('query') == 'async')
                {
                    $start_date = strtotime(vds_request('start_date', '', 'post'));
                    $end_date = strtotime(vds_request('end_date', date('Ymd'), 'post'));
                    $type = vds_request('type', '', 'post');
                    $results = array('status' => 0);
                    if($start_date != FALSE && $end_date != FALSE && $end_date > $start_date && in_array($type, array('browser', 'platform')))
                    {
                        $stats_model = new visitor_stats_model();
                        if($stats_data = $stats_model->stats_period_terminal($type, $start_date, $end_date))
                        {
                            $results = array
                            (
                                'status' => 1,
                                'data' => $stats_data,
                            );
                        }
                    }
                    echo json_encode($results);
                }
                else
                {
                    $this->tpl_display('operation/stats_terminal.html');
                }

            break;
            
            case 'area':
            
                if(vds_request('query') == 'async')
                {
                    $start_date = strtotime(vds_request('start_date', '', 'post'));
                    $end_date = strtotime(vds_request('end_date', date('Ymd'), 'post'));
                    $results = array('status' => 0);
                    if($start_date != FALSE && $end_date != FALSE && $end_date > $start_date)
                    {
                        $stats_model = new visitor_stats_model();
                        if($stats_data = $stats_model->stats_period_area($start_date, $end_date))
                        {
                            $results = array
                            (
                                'status' => 1,
                                'data' => $stats_data,
                            );
                        }
                    }
                    echo json_encode($results);
                }
                else
                {
                    $this->tpl_display('operation/stats_area.html');
                }
            
            break;
            
            case 'traffic':
            default:
                
                if(vds_request('query') == 'async')
                {
                    $start_date = strtotime(vds_request('start_date', '', 'post'));
                    $end_date = strtotime(vds_request('end_date', date('Ymd'), 'post'));
                    $results = array('status' => 0);
                    if($start_date != FALSE && $end_date != FALSE && $end_date > $start_date)
                    {
                        $stats_model = new visitor_stats_model();
                        if($stats_data = $stats_model->stats_period_traffic($start_date, $end_date, 'dateline DESC'))
                        {
                            $results = array
                            (
                                'status' => 1,
                                'data' => $stats_data,
                            );
                        }
                    }
                    echo json_encode($results);
                }
                else
                {
                    $stats_model = new visitor_stats_model();
                    $this->latest = $stats_model->stats_period_traffic(strtotime('yesterday'), strtotime('today'), 'dateline DESC');
                    $this->tpl_display('operation/stats_traffic.html');
                }
        }
    }
    
}