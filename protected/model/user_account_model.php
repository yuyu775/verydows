<?php
class user_account_model extends Model
{
    public $table_name = 'user_account';
    
    public $rules = array
    (
        'balance' => array
        (
            'is_decimal' => array(TRUE, '余额值格式不正确'),
        ),
        'points' => array
        (
            'is_nonegint' => array(TRUE, '积分值格式不正确'),
        ),
        'exp' => array
        (
            'is_nonegint' => array(TRUE, '经验值格式不正确'),
        ),
    );
    
    /**
     * 更新账户数据
     * @param  $user_id  用户ID
     * @param  $data     需要调整的数据
     */
    public function update_account($user_id, $data)
    {
        $sql = "UPDATE {$this->table_name}
                SET
                    balance = balance + :balance,
                    points = points + :points,
                    exp = exp + :exp
                WHERE
                    user_id = :user_id
               ";
               
        $binds = array(':balance' => $data['balance'], ':points' => $data['points'], ':exp' => $data['exp'], ':user_id' => $user_id);
        
        if($this->execute($sql, $binds) > 0)
        {
            
        }
        
        return FALSE;
    }

}