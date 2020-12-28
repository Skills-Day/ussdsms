<?php
    include_once 'sms.php';
    include_once 'db.php';

    $phoneNumber = $_POST['phoneNumber'];
    $shortCode = $_POST['shortCode'];
    $keyword = $_POST['keyword'];
    $updateType = $_POST['updateType'];

    $sms = new Sms($phoneNumber);

    $db = new DBConnector();
    $pdo = $db->connectToDB();

    if($updateType === "Addition"){
        $sms->subscribeUser($pdo,$shortCode,$keyword);
    }else{
        $sms->unSubscribeUser($pdo, $shortCode,$keyword);
    }

?>