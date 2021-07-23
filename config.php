<?php

class Config{

    public $list;

    public $webServer = array(  
        "name" => "Web Server",
        "host" => "localhost",
        "port" => 80,
        "protocol" => "tcp"
    );
    
    public $mailServerIn = array(
        "name" => "Email Server (incoming)",
        "host" => "localhost",
        "port" => 993,
        "protocol" => "tcp"
    );
    
    public $mailServerOut = array(
        "name" => "Email Server (outgoing)",
        "host" => "localhost",
        "port" => 587,
        "protocol" => "tcp"
    );
    
    public $ftpServer = array(
        "name" => "FTP Server",
        "host" => "localhost",
        "port" => 21,
        "protocol" => "tcp"
    );
    
   public $databaseServer = array(
        "name" => "Database Server",
        "host" => "localhost",
        "port" => 3306,
        "protocol" => "tcp"
    );
    
    private $ssh = array(
        "name" => "SSH",
        "host" => "localhost",
        "port" => 22,
        "protocol" => "tcp"
    );
    
    public function setList()
    {
      return  $this->list  = array(
            $this->webServer,
            $this->mailServerIn,
            $this->mailServerOut,
            $this->ftpServer,
            $this->databaseServer, 
            $this->ssh
        );

        
    }
    
}