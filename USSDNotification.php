<?php
    include_once 'db.php';
    $date = $_POST['date'];
    $sessionId = $_POST['sessionId'];
    $serviceCode = $_POST['serviceCode'];
    $networkCode = $_POST['networkCode'];
    $phoneNumber = $_POST['phoneNumber'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $durationInMillis  = $_POST['durationInMillis'];
    $input = $_POST['input'];
    $lastAppResponse  = $_POST['lastAppResponse'];
    $errorMessage = $_POST['errorMessage'];


    $db = new DBConnector();
    $pdo = $db->connectToDB();
    saveUssdNotification($pdo,$date, $sessionId,$serviceCode,$networkCode, $phoneNumber, $status,$cost, $durationInMillis,$input,$lastAppResponse,$errorMessage);
   
    function saveUssdNotification($pdo,$date, $sessionId,$serviceCode,$networkCode, 
    $phoneNumber, $status,$cost, $durationInMillis,$input,$lastAppResponse,$errorMessage){
        $stmt = $pdo->prepare('insert into ussdnotifications (date_,sessionId,serviceCode,networkCode,
        phoneNumber,status,cost,durationInMillis,input,lastAppResponse,errorMessage) 
        values (?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$date,$sessionId,$serviceCode,$networkCode, $phoneNumber,$status,
        $cost,$durationInMillis,$input,$lastAppResponse,$errorMessage]);
        $stmt=null;
    }
?>