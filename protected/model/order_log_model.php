<?php
class order_log_model extends Model
{
    public $table_name = 'order_log';
    
    public $operate_map = array
    (   
        'consignee' => '更改了收件人信息',
        'amount' => '更改了订单金额',
        'cancel' => '取消了该笔订单交易',
        'resume' => '恢复了该笔订单交易',
    );
    /**
     * 记录订单日志
     * @param $order_id 订单号
     * @param $operate 操作类型
     * @param $cause 原因/备注
     */
    public function record($order_id, $operate, $cause)
    {
        $data = array
        (
            'order_id' => $order_id,
            'admin_id' => $_SESSION['admin']['user_id'],
            'operate' => $operate,
            'cause' => $cause,
            'dateline' => time(),
        );
        $this->create($data);
    }
}
