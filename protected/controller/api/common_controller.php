<?php
class common_controller extends Controller
{
    public function action_area()
    {
        $province = vds_request('province', 0, 'get');
        $city = vds_request('city', 0, 'get');
        $area = new area();
        echo json_encode($area->get_children($province, $city));
    }
    
    public function action_stats()
    {
        if(!empty($GLOBALS['cfg']['visitor_stats']))
        {
            $data = array
            (
                'sessid' => vds_request('vds_uvid', '', 'cookie'),
                'ip' => vds_request('ip', '', 'post'),
                'referrer' => vds_request('referrer', '', 'post'), 
                'platform' => vds_request('platform', 0, 'post'),
                'browser' => vds_request('browser', 0, 'post'),
                'area' => vds_request('area', '', 'post'),
            );
            
            $stats_model = new visitor_stats_model();
            $stats_model->do_stats($data);
        }
    }
    
    public function action_jstry(){echo 1;}
}