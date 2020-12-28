<?php
    class Transaction{
        protected $amount;
        protected $ttype;

        function __construct($amount, $ttype)
        {
            $this->amount = $amount;
            $this->ttype = $ttype;
        }

        public function getAmount(){
            return $this->amount;
        }

        public function getTType(){
            return $this->ttype;
        }

        public function sendMoney($pdo, $uid, $ruid, $newSenderBalance, $newReceiverBalance){
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            try{
                $pdo->beginTransaction();
                $stmtT = $pdo->prepare("insert into transaction (amount, uid, ruid, ttype) values(?,?,?,?)");
                $stmtU = $pdo->prepare("update user set balance=? where uid=?");

                $stmtT->execute([$this->getAmount(), $uid, $ruid, $this->getTType()]);
                $stmtU->execute([$newSenderBalance,$uid]);
                $stmtU->execute([$newReceiverBalance,$ruid]);

                $pdo->commit();
                return true;
            }catch(Exception $e){
                $pdo->rollBack();
                return "An error was encountered";
            }
        }

        public function withDrawCash($pdo, $uid, $aid, $newBalance){
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,FALSE);  
            //$pdo->setAttribute (PDO::ATTR_AUTOCOMMIT,FALSE)          
            try {
                $pdo->beginTransaction();
                
                //prepare queries
                $stmtT = $pdo->prepare("insert into transaction (amount, uid, aid, ttype) values (?,?,?,?)");
                $stmtU = $pdo->prepare("update user set balance=? where uid=?");
                
                //execute queries 
                $stmtT->execute([$this->getAmount(), $uid, $aid, $this->getTType()]);
                $stmtU->execute([$newBalance,$uid]);
                $pdo->commit();
                return true;
            } catch (PDOException $e){
                $pdo->rollBack();
                //echo $e->getMessage();
                return "An error occured";
            }
        }
    }

?>