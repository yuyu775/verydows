<?php
class aftersales_model extends Model
{
    public $table_name = 'aftersales';

    public $type_map = array('报修', '换货', '退货');
    
    public $status_map = array('关闭', '开启', '完成');
    
    public $rules = array
    (
        'cause' => array
        (
            'min_length' => array(15, '原因描述不能少于15个字符'),
            'max_length' => array(600, '原因描述不能超过600个字符'),
        ),
        'mobile_no' => array
        (
            'is_moblie_no' => array(TRUE, '请填写一个有效的手机号码'),
        )
    );
    
    public $addrules = array
    (
        'type' => array
        (
            'addrule_valid_type' => '请选择一个有效的处理类型',
        ),
    );
    
    //自定义验证器：检查处理类型是否有效
    public function addrule_valid_type($val)
    {
        return isset($this->type_map[$val]);
    }
    
    /**
     * 检查商品申请售后是否有效
     */
    public function check_apply_valid($user_id, $order_id, $goods_id, $goods_qty = null)
    {
        $where = " WHERE a.user_id = :user_id AND a.order_id = :order_id AND b.goods_id = :goods_id";
        $binds = array(':user_id' => $user_id, ':order_id' => $order_id, ':goods_id' => $goods_id);
        if($goods_qty != null)
        {
            if(intval($goods_qty) == 0) return FALSE;
            $where .= " AND b.goods_qty >= :goods_qty";
            $binds['goods_qty'] = $goods_qty;
        }
        $tblpre = $GLOBALS['mysql']['MYSQL_DB_TABLE_PRE'];
        $sql = "SELECT a.order_id, b.goods_id
                FROM {$tblpre}order AS a
                INNER JOIN {$tblpre}order_goods AS b
                ON a.order_id = b.order_id
                {$where}
               ";
        
        if($this->query($sql, $binds)) return TRUE;
        return FALSE;
    }

}
