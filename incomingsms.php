<?php
       include_once 'db.php';
       include_once 'util.php';
       include_once 'user.php';
       
       //receive data from the gateway 
       $phoneNumber = $_POST['from'];
       $text = $_POST['text']; //name pin; John 1234

       $user = new User($phoneNumber);
       $db = new DBConnector();
       $pdo = $db->connectToDB();

       $text = explode(" ", $text);
       $user->setName($text[0]);
       $user->setPin($text[1]);
       $user->setBalance(Util::$USER_BALANCE);

       $user->register($pdo);

      
?>