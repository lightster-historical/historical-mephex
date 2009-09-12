<?php


class MXT_TickLogger
{    
    protected $tickCount;
    
    protected $lastClass;
    protected $lastMethod;
    
    protected $classTickCount;
    protected $methodTickCount;
    protected $functionTickCount;    
    
    protected $classTime;
    protected $methodTime;
    
    protected $deepestBacktrace;
    protected $deepestBacktraceLength;
    
    
    public function __construct()
    {
        $this->tickCount = 0;
        
        $this->lastClassTime = 0;
        $this->lastClass = null;
        $this->lastMethodTime = 0;
        $this->lastMethod = null;
        
        $this->classTickCount = array();
        $this->methodTickCount = array();
        $this->functionTickCount = array();
        $this->classTimes = array();
        $this->methodTimes = array();
        $this->functionTimes = array();
        
        $this->deepestBacktrace = array();
        $this->deepestBacktraceLength = -1;
        
        declare(ticks=1);
    }
    
    
    public function start()
    {
        register_tick_function(array($this, 'tickTock'));
    }
    
    public function stop()
    {
        unregister_tick_function(array($this, 'tickTock'));
    }


    public function getTickCount()
    {
        return $this->tickCount;
    }


    protected function updateDeepestBacktrace()
    {    
        $backtrace = debug_backtrace();
        $len = count($backtrace);
        if($len > $this->deepestBacktraceLength)
        {
            $this->deepestBacktraceLength = $len;
            $this->deepestBacktrace = $backtrace;
        }
    }

    public function getDeepestBacktrace()
    {
        return $this->deepestBacktrace;
    }

    public function getDeepestBacktraceLength()
    {
        return $this->deepestBacktraceLength;
    }
    
    
    protected function updateClassStats()
    {
        $time = $this->lastClassTime;
            
        $backtrace = debug_backtrace();
        if(count($backtrace) < 3)
            return;
            
        $level = $backtrace[2];
            
        if(!is_null($this->lastClass))
        {
            if(!array_key_exists($this->lastClass, $this->classTimes))
                $this->classTimes[$this->lastClass] = 0;
            
            $this->classTimes[$this->lastClass] += $time;
        }
                
        if(array_key_exists('class', $level))
        {
            $class = strtolower($level['class']);
            
            if(!array_key_exists($class, $this->classTickCount))
                $this->classTickCount[$class] = 1;
            else
                ++$this->classTickCount[$class];
            
            $this->lastClass = $class;
        }
        else
            $this->lastClass = null;
    }
    
    public function getClassTimes()
    {
        return $this->classTimes;
    }
    
    public function getClassTickCount()
    {
        return $this->classTickCount;
    }
    
    
    protected function updateFunctionStats()
    {
        $time = $this->lastMethodTime;
        
        $backtrace = debug_backtrace();
        if(count($backtrace) < 3)
            return;

        $level = $backtrace[2];
            
        if(is_array($this->lastMethod))
        {
            list($class, $method) = $this->lastMethod;
            
            if(!array_key_exists($class, $this->methodTimes)
                || !array_key_exists($method, $this->methodTimes[$class]))
                $this->methodTimes[$class][$method] = 0;
            
            $this->methodTimes[$class][$method] += $time;
        }
        else if(!is_null($this->lastMethod))
        {
            if(!array_key_exists($this->lastMethod, $this->functionTimes))
                $this->functionTimes[$this->lastMethod] = 0;
            
            $this->functionTimes[$this->lastMethod] += $time;
        }

        if(array_key_exists('class', $level))
        {
            $class = strtolower($level['class']);
            $func = strtolower($level['function']);
            if(!array_key_exists($class, $this->methodTickCount)
                || !array_key_exists($func, $this->methodTickCount[$class]))
                $this->methodTickCount[$class][$func] = 1;
            else
                ++$this->methodTickCount[$class][$func];
                
            $this->lastMethod = array($class, $func);
        }
        else
        {
            $func = strtolower($level['function']);
            if(!array_key_exists($func, $this->functionTickCount))
                $this->functionTickCount[$func] = 1;
            else
                ++$this->functionTickCount[$func];
                
            $this->lastMethod = $func;
        }
    }
    
    public function getMethodTimes()
    {
        return $this->methodTimes;
    }
    
    public function getMethodTickCount()
    {
        return $this->methodTickCount;
    }
    
    public function getFunctionTimes()
    {
        return $this->functionTimes;
    }
    
    public function getFunctionTickCount()
    {
        return $this->functionTickCount;
    }
    
    
    public function tickTock()
    {
        if(true)
        {
        // stop the timer ASAP to     prevent too much skewing of time
            $this->lastClassTime = microtime(true) - $this->lastClassTime;
            // stop the timer ASAP to prevent too much skewing of time
            $this->lastMethodTime = microtime(true) - $this->lastMethodTime;
        
            $this->updateClassStats();
            $this->updateFunctionStats();
            $this->updateDeepestBacktrace();
        }
        
        ++$this->tickCount;
        
        $this->lastClassTime = microtime(true);
        $this->lastMethodTime = microtime(true);
    }
}



?>
