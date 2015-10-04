<?php

$config = array(
    "platform_url" => "http://example.com",

    "database" => array(

        "name" => "my_database",

        "host" => "localhost",

        "user" => "root",

        "pass" => ""
    ),

    "user_database" => array(

        "name" => "user_database",

        "host" => "localhost",

        "user" => "root",

        "pass" => ""
    ),

    // Could be sendmail or smtp
    "mail_method" => "sendmail",

    // Required only when mail_method = "smtp"
    "smtpmail_settings" => array (

        'host' => 'mail.example.com',

        'username' => 'info@exmple.com',

        'password' => 'password',

        // Could be "ssl" or "tls" or "nothing"
        'secure' => ''
    )
);