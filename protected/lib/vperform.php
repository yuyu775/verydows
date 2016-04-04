<?php
class vperform
{ 
    private $startmark = array('time' => 0, 'memory' => 0);
    private $stopmark = array('time' => 0, 'memory' => 0);
    public $consuming = array();
    
    public function start()
    {
        $this->startmark = array('time' => microtime(true), 'memory' => memory_get_usage());
    }
    
    public function stop()
    {
        $this->stopmark = array('time' => microtime(true), 'memory' => memory_get_usage());
    }
    
    public function reckon()
    {
        $startmark = $this->startmark;
        $stopmark = $this->stopmark;
        
        return $this->consuming = array
        (
            'time' => round($stopmark['time'] - $startmark['time'], 4) . ' 秒',
            'memory' => ($stopmark['memory'] - $startmark['memory']) / 1024 . ' KB',
        );
    }
} 



?>
