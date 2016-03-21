<?php
class user_actinfo_model extends Model
{
    public $table_name = 'user_actinfo';
    
    public function update_row($user_id, $last_date, $last_ip)
    {
        return $this->execute("UPDATE {$this->table_name} SET last_date = {$last_date}, last_ip = '{$last_ip}' WHERE user_id = {$user_id}");
    }
}