<?php
        include_once 'db.php';
        include_once 'sms.php';


        $shortcode = $_POST['shortcode'];
        $keyword = $_POST['keyword']; 

        $db = new DBConnector();
        $pdo = $db->connectToDB();
    
        $sms = new Sms('073XXX');

        echo json_encode($sms->fecthNewSubscribers($pdo,$shortcode,$keyword));
?>