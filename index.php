<?php
    // http://6d576a762ed0.ngrok.io/ussdsms/index.php
    include_once 'menu.php';
    include_once 'db.php';
    include_once 'user.php';

    // Read the variables sent via POST from our API
    $sessionId   = $_POST["sessionId"];
    $serviceCode = $_POST["serviceCode"];
    $phoneNumber = $_POST["phoneNumber"];
    $text        = $_POST["text"];

   
    $user = new User($phoneNumber);
    $db = new DBConnector();
    $pdo = $db->connectToDB();

    //Create object for Menu class 
    $menu = new Menu();
    $text = $menu->middleware($text, $user, $sessionId, $pdo);
    
    if($text == "" && $user->isUserRegistered($pdo) == true){
         //user is registered and string is is empty
        echo "CON " . $menu->mainMenuRegistered($user->readName($pdo));
    }else if($text == "" && $user->isUserRegistered($pdo) == false){
         //user is unregistered and string is is empty
         $menu->mainMenuUnRegistered();

    }else if($user->isUserRegistered($pdo) == false){
        //user is unregistered and string is not empty
        $textArray = explode("*", $text);
        switch($textArray[0]){
            case 1: 
                $menu->registerMenu($textArray, $phoneNumber,$pdo);
            break;
            default:
                echo "END Invalid choice. Please try again";
        }
    }else{
        //user is registered and string is not empty
        $textArray = explode("*", $text);
        switch($textArray[0]){
            case 1: 
                $menu->sendMoneyMenu($textArray,$user,$pdo,$sessionId);
            break;
            case 2: 
                $menu->withdrawMoneyMenu($textArray,$user,$pdo);
            break;
            case 3:
                $menu->checkBalanceMenu($textArray,$user,$pdo);
                break;
            default:
                $ussdLevel = count($textArray) - 1;
                $menu->persistInvalidEntry($sessionId,$user, $ussdLevel,$pdo);
                echo "CON Inavalid menu\n" . $menu->mainMenuRegistered($user->readName($pdo));
        }
    }

    

?>