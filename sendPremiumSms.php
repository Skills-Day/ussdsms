<?php
    include_once 'db.php';
    include_once 'sms.php';


    $sms = new Sms('08634533');
    $db = new DBConnector();
    $pdo = $db->connectToDB();

    $shortcode = $_POST['shortcode'];
    $keyword = $_POST['keyword'];
    $message = $_POST['message'];

    $response = $sms->sendPremiumSms($pdo,$shortcode, $keyword, $message);

    
    echo json_encode($response);

?>