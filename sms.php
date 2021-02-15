<?php
    require 'vendor/autoload.php';
    use AfricasTalking\SDK\AfricasTalking;

    include_once 'util.php';
    include_once 'db.php';


    class Sms {
        protected $phone;
        protected $AT;

        function __construct($phone)
        {
            $this->phone = $phone;
            $this->AT = new AfricasTalking(Util::$API_USERNAME, Util::$API_KEY);
        }

        public function getPhone(){
            return $this->phone;
        }

        public function sendSMS($message, $recipients){
            //get the sms service
            $sms = $this->AT->sms();
            //use the service 
            $result = $sms->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => Util::$SMS_SHORTCODE,
                'keyword' => Util::$SMS_SHORTCODE_KEYWORD
            ]);
            return $result;
        }

        public function fetchRecipients (){
            //Read all user phone numbers
            $db = new DBConnector();
            $pdo = $db->connectToDB();
            //prepare an sql query 
            $stmt = $pdo->prepare('select phone from user');
            $stmt->execute();
            $result = $stmt->fetchAll();
            //hopinf that there were records to simplify logic
            //result has an array of objects
            $recipients = array();
           foreach($result as $row){
                array_push($recipients,$row['phone']);                 
           }
           return join(",", $recipients);
        }

        public function subscribeUser ($pdo, $shortcode, $keyword){
            $stmt = $pdo->prepare('insert into subscribers (phoneNumber, shortcode,keyword,isActive) values(?,?,?,?)');
            $stmt->execute([$this->getPhone(), $shortcode, $keyword, 1]);
            $stmt = null;
        }


        public function unSubscribeUser($pdo, $shortcode, $keyword){
            $stmt = $pdo->prepare('update subscribers set isActive=? where phoneNumber=? and shortcode=? and keyword=?');
            $stmt->execute([0,$this->getPhone(),$shortcode, $keyword]);
            $stmt = null;
        }

        public function sendPremiumSms ($pdo, $shortcode, $keyword, $message){
            $recipients = $this->fetchActivePhoneNumbers($pdo, $shortcode, $keyword);
            $content = $this->AT->content();
            $response = $content->send([
                'message'=> $message,
                'to'=>$recipients,
                'from'=>$shortcode,
                'keyword' => $keyword
            ]);
            return $response;
        }

        public function fetchActivePhoneNumbers ($pdo, $shortcode, $keyword){
            $stmt= $pdo->prepare('select phoneNumber from subscribers where isActive=? and shortcode=? and keyword=?');
            $stmt->execute([1, $shortcode, $keyword]);
            $activePhoneNumbers = $stmt->fetchAll();
            $recipients = array();

            foreach($activePhoneNumbers as $phone){
                array_push($recipients, $phone['phoneNumber']);
            }
            return $recipients;
        }
        
        public function subscribeUserWithToken ($shortcode, $keyword, $phone){
            $content = $this->AT->content();
            $checkoutToken = $this->getToken($phone);
            $response = $content->createSubscription([
                'shortCode'=>$shortcode,
                'keyword'=>$keyword,
                'phoneNumber'=>$phone,
                'checkoutToken'=>$checkoutToken
            ]);
            return $response;
        }

        public function getToken($phone) {
            $token = $this->AT->token();
            $tokenResult = $token->createCheckoutToken([
                'phoneNumber'=>$phone
            ]);

            $checkoutToken = $tokenResult['data']->token;
            return $checkoutToken;
        }        

        
        public function fecthNewSubscribers($pdo, $shortcode, $keyword){
            $content = $this->AT->content();

            $responseArray = $content->fetchSubscriptions([
                'shortCode'=>$shortcode,
                'keyword'=>$keyword,
                'lastReceivedId'=>0,
            ])['data']->responses;

            foreach($responseArray as $res){
                $sms = new Sms($res->phoneNumber);
                $sms->subscribeUser($pdo,$shortcode,$keyword);
            }

            return $responseArray;
        }
    }

?>