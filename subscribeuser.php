<?php
    include_once 'db.php';
    include_once 'sms.php';

    $shortcode = $_POST['shortcode'];
    $keyword = $_POST['keyword'];
    $phoneNumber = $_POST['phoneNumber'];

    $db = new DBConnector();
    $pdo = $db->connectToDB();

    $sms = new Sms($phoneNumber);

    echo json_encode($sms->subscribeUserWithToken($shortcode,$keyword,$phoneNumber));
?>