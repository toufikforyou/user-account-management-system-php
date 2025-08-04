<?php

namespace Services\Models;

use Services\Database\SLDatabase;

class OtpModel extends SLDatabase
{
    protected function sendingOtp(string $account, int $otp, ?array $details): ?array
    {
        $response = null;

        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            // SQL query and statement binding

            $sql = 'SELECT `ACCOUNT` FROM `SOPNOLIKHI_VERIFY_OTP` WHERE `ACCOUNT`=:account;';

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':account', htmlspecialchars($account), \PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                // insert code here
                $sql1 = 'INSERT INTO `SOPNOLIKHI_VERIFY_OTP`(`ACCOUNT`, `OTP`, `LOCATION`, `DETAILS`) VALUES (:account, :otp, :location, :details);';

                $stmt1 = $conn->prepare($sql1);

                $stmt1->bindParam(':account', htmlspecialchars($account), \PDO::PARAM_STR);
                $stmt1->bindParam(':otp', htmlspecialchars($otp), \PDO::PARAM_STR);
                $stmt1->bindParam(':location', htmlspecialchars(json_encode($details['locations'])), \PDO::PARAM_STR);
                $stmt1->bindParam(':details', htmlspecialchars(json_encode($details['device-info'])), \PDO::PARAM_STR);

                $stmt1->execute();
            } else {
                // update code here
                $sql2 = 'UPDATE `SOPNOLIKHI_VERIFY_OTP` SET `OTP`=:otp, `LOCATION`=:location,`DETAILS`=:details WHERE `ACCOUNT`=:account;';

                $stmt2 = $conn->prepare($sql2);

                $stmt2->bindParam(':otp', htmlspecialchars($otp), \PDO::PARAM_INT);
                $stmt2->bindParam(':location', htmlspecialchars(json_encode($details['locations'])), \PDO::PARAM_STR);
                $stmt2->bindParam(':details', htmlspecialchars(json_encode($details['device-info'])), \PDO::PARAM_STR);
                $stmt2->bindParam(':account', htmlspecialchars($account), \PDO::PARAM_STR);

                $stmt2->execute();
            }

            $response = array(
                'success' => 1,
                'result' => [
                    'account' => $account,
                    'verify' => false,
                    'message' =>  'Otp send successfully',
                    'expired' => date("Y-m-d H:i:s", time() + 300)
                ]
            );

            $conn->commit();
        } catch (\PDOException $e) {
            // Error Handler Response
            $conn->rollBack();

            $response = array('error' => -1, 'result' => 'Something is wrong!');
        }

        return $response;
    }



    protected function verifyOtp(string $account, int $otp, ?array $details): ?array
    {
        $response = null;

        try {
            $conn = $this->connect();
            $conn->beginTransaction();
            // SQL query and statement binding

            $stmt = $conn->prepare('SELECT `ACCOUNT`, `ONUPDATE` FROM `SOPNOLIKHI_VERIFY_OTP` WHERE `ACCOUNT`=:account AND `OTP`=:otp LIMIT 1;');

            $stmt->bindParam(':account', htmlspecialchars($account), \PDO::PARAM_STR);
            $stmt->bindParam(':otp', htmlspecialchars($otp), \PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (strtotime($result['ONUPDATE']) + 300 >= time()) {
                $response = array(
                    'success' => 1,
                    'result' => [
                        'account' => $account,
                        'verify' => true,
                        'message' =>  'Otp verified',
                        'expired' => ''
                    ]
                );
            } else {
                $response = array(
                    'error' => 1,
                    'result' => 'Otp expired'
                );
            }

            $conn->commit();
        } catch (\PDOException $e) {
            // Error Handler Response
            $conn->rollBack();

            $response = array('error' => -1, 'result' => 'Something is wrong!');
        }

        return $response;
    }
}