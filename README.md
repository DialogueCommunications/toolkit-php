# toolkit-php

## About

A PHP client library to send SMS messages through the Dialogue SMS Toolkit API: http://www.dialogue.net/sms_toolkit/

## Install

    git clone git://github.com/DialogueCommunications/toolkit-php.git
    cd toolkit-php

## Example Usage

    <?php 
    error_reporting(E_ALL); 
    ini_set("display_errors", "true"); 
    include("DialoguePartnerToolkitClientLibrary.php"); 
    use 
    \net\dialogue\toolkit\sms\SendSmsClient, 
    \net\dialogue\toolkit\sms\Credentials, 
    \net\dialogue\toolkit\sms\SendSmsRequest, 
    \net\dialogue\toolkit\sms\SendSmsResponse, 
    \net\dialogue\toolkit\sms\Sms; 
    $client = new SendSmsClient( 
        new Credentials("user", "pass") 
    ); 
    $request = new SendSmsRequest( 
        "This is a test message", 
        "447xxxxxxxxx" 
    ); 
    try 
    { 
        $response = $client->sendSms($request); 
        print_r($response); 
    } 
    catch(Exception $e) 
    { 
        echo "Request failed with code " . $e->getCode() .  
            " and message " . $e->getMessage(); 
    }  
    ?>

## References

* [PHP Quick Start Guide][quick_start_guide]

 [quick_start_guide]: http://www.dialogue.net/sms_toolkit/documents/Dialogue_Partner_Toolkit_Quick_Start_Guide_PHP.pdf

## Contribute

If you would like to contribute to toolkit-php then fork the project, implement your feature/plugin on a new branch and send a pull request.
