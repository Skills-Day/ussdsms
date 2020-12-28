<?php
    include_once 'sms.php';

    $sms = new Sms('+3444444');
    $recipients =  $sms->fetchRecipients();
    $response = $sms->sendSMS("We have slashed our money sending fees by 50%", $recipients);
    echo json_encode($response);
?>