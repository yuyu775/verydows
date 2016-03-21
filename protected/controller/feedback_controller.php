<?php
class feedback_controller extends general_controller
{
	public function action_index()
    {
        $user_id = parent::check_acl();
        $feedback_model = new feedback_model();
        $this->feedback_list = array
        (
            'rows' => $feedback_model->find_all(array('user_id' => $user_id), 'created_date DESC', 'fb_id, type, subject, created_date, status', array(vds_request('page', 1), 10, 10)),
            'paging' => $feedback_model->page,
            'type_map' => $feedback_model->type_map,
        );
        parent::tpl_display('user_feedback_list.html');
	}
    
    public function action_details()
    {
        parent::check_acl();
        $fb_id = vds_request('id', null, 'get');
        $feedback_model = new feedback_model();
        if($feedback = $feedback_model->find(array('fb_id' => $fb_id)))
        {
            $type_map = $feedback_model->type_map;
            $feedback['type'] = $type_map[$feedback['type']];
            $this->feedback = $feedback;
            $message_model = new feedback_message_model();
            $this->message_list = $message_model->find_all(array('fb_id' => $fb_id), 'dateline ASC');
            parent::tpl_display('user_feedback_details.html');
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
    
    public function action_apply()
    {
        $user_id = parent::check_acl();
        if(vds_request('step', null, 'get') == 'submit')
        {
            $data = array
            (
                'user_id' => $user_id,
                'type' => vds_request('type', null, 'post'),
                'subject' => trim(strip_tags(vds_request('subject', '', 'post'))),
                'content' => trim(strip_tags(vds_request('content', '', 'post'))),
                'mobile_no' => trim(vds_request('mobile_no', '', 'post')),
                'created_date' => $_SERVER['REQUEST_TIME'],
                'status' => 1,
            );
                
            $feedback_model = new feedback_model();
            $verifier = $feedback_model->verifier($data);
            if(TRUE === $verifier)
            {
                $feedback_model->create($data);
                parent::prompt('success', '提交成功', url('feedback', 'index'));
            }
            else
            {
                parent::prompt('error', $verifier);
            }
        }
        else
        {
            $feedback_model = new feedback_model();
            $this->type_map = $feedback_model->type_map;
            $this->status_map = $feedback_model->status_map;
            parent::tpl_display('user_feedback_apply.html');
        }
    }
    
    public function action_messaging()
    {
        $user_id = parent::check_acl();
        $fb_id = vds_request('id');
        $feedback_model = new feedback_model();
        if($feedback_model->find(array('fb_id' => $fb_id, 'user_id' => $user_id, 'status' => 1)))
        {
            $data = array
            (
                'fb_id' => $fb_id,
                'content' => trim(strip_tags(vds_request('content', '', 'post'))),
                'dateline' => $_SERVER['REQUEST_TIME'],
            );
                        
            $message_model = new feedback_message_model();
            $verifier = $message_model->verifier($data);
            if(TRUE === $verifier)
            {
                $message_model->create($data);
                parent::prompt('success', '发送消息成功', url('feedback', 'details', array('id' => $fb_id)));
            }
            else
            {
                parent::prompt('error', $verifier);
            }
        }
        else
        {
            vds_jump(url('main', '404'));
        }
    }
}