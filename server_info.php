<?php

require('config.php');

class fileServer extends Config {
    
    
    private $response;
    private $config;
    

    public function __construct () {
         $this->config['services'] = array(
                "show_port" => true,
                "list" => $this->setList()         
         );
    }
    
    public function getResponse(){
        echo json_encode($this->response);
    }
    
    public function memoryTest(){
        
        // Starting clock time in seconds
        $start_time = microtime(true);
        
        try {
            
            define('MEMORY_TEST', true);
            $memoryTest = true;
            
        } catch (Exception $e) {
            
            $this->response["memory"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
        
        try {
            
            $memUsage = $this->getServerMemoryUsage(false);
            
            $this->response["memory"]["status"] = defined('MEMORY_TEST') && !empty($memoryTest);
            $this->response["memory"]["total"] = $this->getSize($memUsage["total"]);
            $this->response["memory"]["used"] = $this->getSize($memUsage["used"]);
            $this->response["memory"]["free"] = $this->getSize($memUsage["free"]);
            $this->response["memory"]["percentage"] = number_format($memUsage["percentage"], 2) . "%";
            $this->response["memory"]["data"] = $memUsage;
            
            
        } catch (Exception $e) {
            
            $this->response["memory"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
        
        // End clock time in seconds
        $end_time = microtime(true);
  
        // Calculate script execution time
        $this->response['memory']['total_time'] = ($end_time - $start_time);
        
        try { 
            
            unset($start_time);
            unset($end_time);
            unset($memoryTest);
            
        } catch (Exception $e) {
            
            $this->response["memory"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }    
        
    }
    
    public function cpuTest(){
        
        // Starting clock time in seconds
        $start_time = microtime(true);
            
            try {
            
            $operating_system = PHP_OS_FAMILY;
        
            if ($operating_system === 'Windows') {
            
                // Win CPU
                $wmi = new COM('WinMgmts:\\\\.');
                $cpus = $wmi->InstancesOf('Win32_Processor');
                $cpuload = 0;
                $cpu_count = 0;
                foreach ($cpus as $key => $cpu) {
                    $cpuload += $cpu->LoadPercentage;
                    $cpu_count++;
                }
            
                // WIN MEM
                /*$res = $wmi->ExecQuery('SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem');
                $mem = $res->ItemIndex(0);
                $memtotal = round($mem->TotalVisibleMemorySize / 1000000,2);
                $memavailable = round($mem->FreePhysicalMemory / 1000000,2);
                $memused = round($memtotal-$memavailable,2);*/
            
                // WIN CONNECTIONS
                /*$connections = shell_exec('netstat -nt | findstr :80 | findstr ESTABLISHED | find /C /V ""'); 
                $totalConnections = shell_exec('netstat -nt | findstr :80 | find /C /V ""'); */
    
            } else if ($operating_system === 'Linux') {
            
                // Linux CPU
                $load = sys_getloadavg();
                $cpuload = $load[0];
                
                // Linux MEM
                /*$free = shell_exec('free');
                $free = (string)trim($free);
                $free_arr = explode("\n", $free);
                $mem = explode(" ", $free_arr[1]);
                $mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
                $mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
                $memtotal = round($mem[1] / 1000000,2);
                $memused = round($mem[2] / 1000000,2);
                $memfree = round($mem[3] / 1000000,2);
                $memshared = round($mem[4] / 1000000,2);
                $memcached = round($mem[5] / 1000000,2);
                $memavailable = round($mem[6] / 1000000,2);*/
                
                // Linux services
                $services = `systemctl --type=service --state=active`;
                
                $services = str_replace(array("\r\n", "\n\r", "\r"), "\n", $services);
                $services = explode("\n", $services);
                    
                $tasks = `ps -aux`;
                
                $tasks = str_replace(array("\r\n", "\n\r", "\r"), "\n", $tasks);
                $tasks = explode("\n", $tasks); 
                
            }
            
                $this->response["cpu"]['percentage'] = number_format(((($load[0] + $load[1] + $load[2]) / 3) * 100), 2) . "%";
                $this->response["cpu"]['services'] = (int)(count($services) -9);
                $this->response["cpu"]['tasks'] = (int)(count($tasks) -1);
                $this->response["cpu"]['data']['load'] = $load;
                $this->response["cpu"]['data']['services'] = $services;
            
            } catch (Exception $e) {
            
            $this->response["cpu"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
        
        // End clock time in seconds
        $end_time = microtime(true);
  
        // Calculate script execution time
        $this->response['cpu']['total_time'] = ($end_time - $start_time);
        
        
    }
    
    public function hardDriveTest(){
        
        // Starting clock time in seconds
        $start_time = microtime(true);
        
        
        $this->response['hardDrive']['status'] = true;
        
        try {
            
            $diskfree = round(disk_free_space("."));
            $disktotal = round(disk_total_space("."));
            $diskused = round($disktotal - $diskfree);
            $diskusage = round($diskused/$disktotal*100);

            $this->response['hardDrive']['percentage'] = number_format(($diskused/$disktotal*100),2)."%";
            
            $this->response['hardDrive']['total'] = $this->getSize($disktotal);
            $this->response['hardDrive']['used'] = $this->getSize(round($disktotal - $diskfree));
            $this->response['hardDrive']['free'] = $this->getSize($diskfree);
            
            $this->response['hardDrive']['data']['total'] = $disktotal;
            $this->response['hardDrive']['data']['used'] = round($disktotal - $diskfree);
            $this->response['hardDrive']['data']['free'] = $diskfree;
            
            $this->response['hardDrive']['data']['percentage'] = ($diskused/$disktotal*100);
        
        } catch (Exception $e) {
            
            $this->response["hardDrive"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
        
        // End clock time in seconds
        $end_time = microtime(true);
  
        // Calculate script execution time
        $this->response['hardDrive']['total_time'] = ($end_time - $start_time);

        
    }
    
    
    public function accessibilityTest(){
        
        // Starting clock time in seconds
        $start_time = microtime(true);
        
            try {
                
                $this->response["accessibility"]['status'] = true;
                
            }  catch (Exception $e) {
            
            $this->response["accessibility"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        } catch (Exception $e) {
            
            $this->response["accessibility"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
            
            try {
            
            $operating_system = PHP_OS_FAMILY;
        
            if ($operating_system === 'Windows') {
            
                // WIN CONNECTIONS
                $connections = shell_exec('netstat -nt | findstr :80 | findstr ESTABLISHED | find /C /V ""'); 
                $totalConnections = shell_exec('netstat -nt | findstr :80 | find /C /V ""');
    
            } else if ($operating_system === 'Linux') {
            
                
                // Linux Connections
                $connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
                $totalConnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 

            }
            
                $this->response["accessibility"]['connections'] = (int)$connections;
                $this->response["accessibility"]['totalConnections'] = (int)$totalConnections;
            
            } catch (Exception $e) {
            
            $this->response["accessibility"]['errors'] =  array_push($errors, 
            array(
                "file" => basename(__FILE__),
                "message" => $e->getMessage(),
                "line" => $e->getLine(),
                "datetime" => date("Y-m-d h:i:s")
                )
        );
            
        }
        
        // End clock time in seconds
        $end_time = microtime(true);
  
        // Calculate script execution time
        $this->response['accessibility']['total_time'] = ($end_time - $start_time);
        
        
    }
    
    
    public function getServerMemoryUsage()
    {
        $memoryTotal = null;
        $memoryFree = null;

        if (stristr(PHP_OS, "win")) {
            // Get total physical memory (this is in bytes)
            $cmd = "wmic ComputerSystem get TotalPhysicalMemory";
            @exec($cmd, $outputTotalPhysicalMemory);

            // Get free physical memory (this is in kibibytes!)
            $cmd = "wmic OS get FreePhysicalMemory";
            @exec($cmd, $outputFreePhysicalMemory);

            if ($outputTotalPhysicalMemory && $outputFreePhysicalMemory) {
                // Find total value
                foreach ($outputTotalPhysicalMemory as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryTotal = $line;
                        break;
                    }
                }

                // Find free value
                foreach ($outputFreePhysicalMemory as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryFree = $line;
                        $memoryFree *= 1024;  // convert from kibibytes to bytes
                        break;
                    }
                }
            }
        }
        else
        {
            if (is_readable("/proc/meminfo"))
            {
                $stats = @file_get_contents("/proc/meminfo");

                if ($stats !== false) {
                    // Separate lines
                    $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
                    $stats = explode("\n", $stats);

                    // Separate values and find correct lines for total and free mem
                    foreach ($stats as $statLine) {
                        $statLineData = explode(":", trim($statLine));

                        //
                        // Extract size (TODO: It seems that (at least) the two values for total and free memory have the unit "kB" always. Is this correct?
                        //

                        // Total memory
                        if (count($statLineData) == 2 && trim($statLineData[0]) == "MemTotal") {
                            $memoryTotal = trim($statLineData[1]);
                            $memoryTotal = explode(" ", $memoryTotal);
                            $memoryTotal = $memoryTotal[0];
                            $memoryTotal *= 1024;  // convert from kibibytes to bytes
                        }

                        // Free memory
                        if (count($statLineData) == 2 && trim($statLineData[0]) == "MemFree") {
                            $memoryFree = trim($statLineData[1]);
                            $memoryFree = explode(" ", $memoryFree);
                            $memoryFree = $memoryFree[0];
                            $memoryFree *= 1024;  // convert from kibibytes to bytes
                        }
                    }
                }
            }
        }

        if (is_null($memoryTotal) || is_null($memoryFree)) {
            return null;
        } else {
                return array(
                    "total" => $memoryTotal,
                    "free" => $memoryFree,
                    "used" => $memoryTotal - $memoryFree,
                    "percentage" => (100 - ($memoryFree * 100 / $memoryTotal))
                );
            
        }
    }




    // nuevos
    public function getSystemInfo() {
        // Hostname
        $hostname = php_uname('n');

        // OS
        if (!($os = shell_exec('/usr/bin/lsb_release -ds | cut -d= -f2 | tr -d \'"\'')))
        {
            if(!($os = shell_exec('cat /etc/system-release | cut -d= -f2 | tr -d \'"\''))) 
            {
                if (!($os = shell_exec('find /etc/*-release -type f -exec cat {} \; | grep PRETTY_NAME | tail -n 1 | cut -d= -f2 | tr -d \'"\'')))
                {
                    $os = 'N.A';
                }
            }
        }
        $os = trim($os, '"');
        $os = str_replace("\n", '', $os);

        // Kernel
        if (!($kernel = shell_exec('/bin/uname -r')))
        {
            $kernel = 'N.A';
        }

        // Uptime
        if (!($totalSeconds = shell_exec('/usr/bin/cut -d. -f1 /proc/uptime'))){
            $uptime = 'N.A';
        }else{
            $uptime = $this->getHumanTime($totalSeconds);
        }

        // Last boot
        if (!($upt_tmp = shell_exec('cat /proc/uptime')))
        {
            $last_boot = 'N.A';
        }else{
            $upt = explode(' ', $upt_tmp);
            $last_boot = date('Y-m-d H:i:s', time() - intval($upt[0]));
        }

        // Current users
        if (!($current_users = shell_exec('who -u | awk \'{ print $1 }\' | wc -l')))
        {
            $current_users = 'N.A';
        }

        // Server datetime
        if (!($server_date = shell_exec('/bin/date'))){
            $server_date = date('Y-m-d H:i:s');
        }
       
        $this->response = array(
            "system" => compact('hostname','os','kernel','uptime', 'last_boot', 'current_users', 'server_date')
         );
        
    }

    public function getCpuInfo(){
        // Number of cores
        $num_cores = $this->getCpuCoresNumber();


        // CPU info
        $model      = 'N.A';
        $frequency  = 'N.A';
        $cache      = 'N.A';
        $bogomips   = 'N.A';
        $temp       = 'N.A';

        if ($cpuinfo = shell_exec('cat /proc/cpuinfo'))
        {
            $processors = preg_split('/\s?\n\s?\n/', trim($cpuinfo));

            foreach ($processors as $processor)
            {
                $details = preg_split('/\n/', $processor, -1, PREG_SPLIT_NO_EMPTY);

                foreach ($details as $detail)
                {
                    list($key, $value) = preg_split('/\s*:\s*/', trim($detail));

                    switch (strtolower($key))
                    {
                        case 'model name':
                        case 'cpu model':
                        case 'cpu':
                        case 'processor':
                            $model = $value;
                        break;

                        case 'cpu mhz':
                        case 'clock':
                            $frequency = $value.' MHz';
                        break;

                        case 'cache size':
                        case 'l2 cache':
                            $cache = $value;
                        break;

                        case 'bogomips':
                            $bogomips = $value;
                        break;
                    }
                }
            }
        }

        if ($frequency == 'N.A')
        {
            if ($f = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_max_freq'))
            {
                $f = $f / 1000;
                $frequency = $f.' MHz';
            }
        }

        $datas = array(
            'model'      => $model,
            'num_cores'  => $num_cores,
            'frequency'  => $frequency,
            'cache'      => $cache,
            'bogomips'   => $bogomips,
        );

        return  $this->response['cpu_info'] = $datas;
    }

    public function getSwap(){
            
        // Free
        if (!($free = shell_exec('grep SwapFree /proc/meminfo | awk \'{print $2}\'')))
        {
            $free = 0;
        }

        // Total
        if (!($total = shell_exec('grep SwapTotal /proc/meminfo | awk \'{print $2}\'')))
        {
            $total = 0;
        }

        // Used
        $used = (int)$total - (int)$free;
        
  
            $percentage = ($total == 0) ?  0 :  100 - ((int)$free / (int)$total * 100);
        

        // Percent used
        $percent_used = 0;
        if ($total > 0)
            $percent_used = 100 - (round($percentage));

            $this->response['swap'] =  array(
                    'used'          => $this->getSize((int)$used * 1024),
                    'free'          => $this->getSize((int)$free * 1024),
                    'total'         => $this->getSize((int)$total * 1024),
                    'percentage'  => $percent_used,
                    'data' => array(
                        'used'          => (int)$used,
                        'free'          => (int)$free,
                        'total'         => (int)$total,
                        'percentage'    => $percentage
                    )
             );
        
    }

    public function getServicesStatus(){
        $datas = array();

      

        $available_protocols = array('tcp', 'udp');

        $show_port = $this->config['services']['show_port'];

        if (count( $this->config['services']['list']) > 0)
        {
            foreach ($this->config['services']['list'] as $service)
            {
                $host     = $service['host'];
                $port     = $service['port'];
                $name     = $service['name'];
                $protocol = isset($service['protocol']) && in_array($service['protocol'], $available_protocols) ? $service['protocol'] : 'tcp';

                if ($this->scanPort($host, $port, $protocol))
                    $status = 1;
                else
                    $status = 0;

                $datas[] = array(
                    'port'      => $show_port === true ? $port : '',
                    'name'      => $name,
                    'status'    => $status,
                );
            }

            return $this->response['serviceStatus'] = $datas;
        }

    }

    public function getLoadAvg(){
        if (!($load_tmp = shell_exec('cat /proc/loadavg | awk \'{print $1","$2","$3}\''))){
            $load = array(0, 0, 0);
        }
        else
        {
            // Number of cores
            $cores = $this->getCpuCoresNumber();

            $load_exp = explode(',', $load_tmp);

            $load = array_map(
                function ($value, $cores) {
                    $v = (int)((int)$value * 100 / (int)$cores);
                    if ($v > 100)
                        $v = 100;
                    return $v;
                }, 
                $load_exp,
                array_fill(0, 3, $cores)
            );
        }


        return $this->response['load_average'] = $load;
    }

    public function getCpuCoresNumber(){
        if (!($num_cores = shell_exec('/bin/grep -c ^processor /proc/cpuinfo')))
        {
            if (!($num_cores = trim(shell_exec('/usr/bin/nproc'))))
            {
                $num_cores = 1;
            }
        }

        if ((int)$num_cores <= 0)
            $num_cores = 1;

        return (int)$num_cores;
    }



    public static function getSize($filesize, $precision = 2){
        $units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');

        foreach ($units as $idUnit => $unit)
        {
            if ($filesize > 1024)
                $filesize /= 1024;
            else
                break;
        }
        
        return round($filesize, $precision).' '.$units[$idUnit].'B';
    }

    public static function getHumanTime($seconds)
    {
        $units = array(
            'year'   => 365*86400,
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60,
            // 'second' => 1,
        );
     
        $parts = array();
     
        foreach ($units as $name => $divisor){
            $div = floor((int)$seconds / (int)$divisor);
     
            if ($div == 0)
                continue;
            else
                if ($div == 1)
                    $parts[] = $div.' '.$name;
                else
                 $parts[] = $div.' '.$name.'s';
               //  $seconds %= ((int)$divisor);
        }
     
        $last = array_pop($parts);
     
        if (empty($parts))
            return $last;
        else
            return join(', ', $parts).' and '.$last;
    }


    public function scanPort($host, $port, $protocol = 'tcp', $timeout = 3)
    {
        if ($protocol == 'tcp')
        {
            $handle = @fsockopen($host, $port, $errno, $errstr, $timeout);

            if ($handle)
                return true;
            else
                return false;
        }
        elseif ($protocol == 'udp')
        {
            $handle = @fsockopen('udp://'.$host, $port, $errno, $errstr, $timeout);

            socket_set_timeout($handle, $timeout);

            $write = fwrite($handle, 'x00');

            $startTime = time();

            $header = fread($handle, 1);

            $endTime = time();

            $timeDiff = $endTime - $startTime; 
            
            fclose($handle);

            if ($timeDiff >= $timeout)
                return true;
            else
                return false;
        }

        return false;
    }

}


$fileServer = new fileServer();

$fileServer->getSystemInfo();
$fileServer->memoryTest();
$fileServer->getSwap();
$fileServer->getServicesStatus();
$fileServer->cpuTest();
$fileServer->getCpuInfo();
$fileServer->getLoadAvg();
$fileServer->hardDriveTest();
$fileServer->accessibilityTest();

$fileServer->getResponse();

?>