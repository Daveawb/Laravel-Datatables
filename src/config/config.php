<?php

return array(

   /*
    |--------------------------------------------------------------------------
    | Datatables version
    |--------------------------------------------------------------------------
    |
    | As there are major differences between dataTables 1.9.x and 1.10.x we
    | need to define which one we are using to define the syntax the package
    | uses for both input and responses.
    | 
    | Available options: "1.9" and "1.10"
    */
    
    "version" => "1.9",
    
    /*
    |--------------------------------------------------------------------------
    | Datatables database options
    |--------------------------------------------------------------------------
    |
    | All database options are configured here. You can add extra configurations
    | to map to different connections if you wish as well as registering custom
    | drivers amongst other things.
    */
    
    "database" => array(
        
        /*
        |--------------------------------------------------------------------------
        | Datatables default driver
        |--------------------------------------------------------------------------
        |
        | Set this to the default driver you want to use. 'laravel' is the standard
        | option and uses the standard Eloquent / Fluent configuration and as such
        | has no connections mapping. If you want to use any other driver switch
        | this to the connection configuration.
        */
        "default" => "laravel",
        
        "connections" => array(
            
            "mongo" => array(
                'host'     => 'localhost',
                'port'     => 27017,
                'username' => 'username',
                'password' => 'password',
                'database' => 'database',
                'options'  => array()
            ),
            
            //The below configuration can be used if you have multiple servers
            //or are using replica sets
            
            /*
            'mongodb' => array(
                'host'     => array('server1', 'server2'),
                'port'     => 27017,
                'username' => 'username',
                'password' => 'password',
                'database' => 'database',
                'options'  => array('replicaSet' => 'replicaSetName')
            ),
            */
        ),
    ),
);
