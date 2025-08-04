<?php

namespace Services\Models;


use Services\Database\SLDatabase;

class SignInModel extends SLDatabase
{
    protected function signInAccount(int $uid, string $password, bool $remember = false, array $others): ?array
    {
        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `UID`, `FULLNAME`, `ACTIVE`, `PASSWORD` FROM `SOPNOLIKHI_USERS` WHERE `UID`=:userId LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $conn->commit();
                return array('error' => 1, 'result' => 'User not found!');
            }

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (intval($result['ACTIVE']) !== 1) {
                $conn->commit();
                return array('error' => 2, 'result' => 'User account isn\'t active. Please contact support team!' . $result['ACTIVE']);
            }


            if (password_verify($password, $result['PASSWORD']) === false) {
                $conn->commit();
                return array('error' => 3, 'result' => 'Password does not match!');
            } else {
                if ($remember) {
                    $timeout =  time() + (86400 * 30);
                } else {
                    $timeout = time() + (86400 * 15);
                }

                $token = $this->generateToken();

                if ($this->saveLoginToken($uid, $token, $timeout, $others)) {
                    // password valid then save details
                    $response = array(
                        'success' => 1,
                        'result' => [
                            'uid' => $result['UID'],
                            'full-name' => $result['FULLNAME'],
                            'token' => $token,
                            'expired' => date("Y-m-d H:i:s", $timeout)
                        ]
                    );
                } else {
                    $response = array("error" => 4, "result" => 'Something is wrong!');
                }
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();

            $response = array("error" => -1, "result" => 'Something is wrong!');
        }

        return $response;
    }

    private function saveLoginToken(int $uid, string $token, int $timeout, array $others): bool
    {
        $deviceId = $others['device-info']['device-id'];
        if ($deviceId === null) {
            $deviceId = "SL" . $uid;
        }

        $conn = $this->connect();

        if ($this->getPreviousLogIn($deviceId, $uid)) {
            $sql2 = 'INSERT INTO `SOPNOLIKHI_USERS_LOGIN`(`UID`, `TOKEN`, `DEVICEID`, `EXPIRED`, `LOCATION`, `DETAILS`) 
                    VALUES (:userId, :token, :deviceId, :expired, :location, :details);';

            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);
            $stmt2->bindParam(":token", htmlspecialchars($token), \PDO::PARAM_STR);
            $stmt2->bindParam(":deviceId", htmlspecialchars($deviceId), \PDO::PARAM_STR);
            $stmt2->bindParam(":expired", htmlspecialchars($timeout), \PDO::PARAM_STR);
            $stmt2->bindParam(":location", htmlspecialchars(json_encode($others['locations'])), \PDO::PARAM_STR);
            $stmt2->bindParam(":details", htmlspecialchars(json_encode($others['device-info'])), \PDO::PARAM_STR);

            if ($stmt2->execute()) {
                return true;
            }
        } else {

            $sql3 = 'UPDATE `SOPNOLIKHI_USERS_LOGIN` SET `TOKEN`=:token ,`EXPIRED`=:expired,`LOCATION`=:location,`DETAILS`=:details WHERE `UID`=:userId AND `DEVICEID`=:deviceId;';

            $stmt3 = $conn->prepare($sql3);
            $stmt3->bindParam(":token", htmlspecialchars($token), \PDO::PARAM_STR);
            $stmt3->bindParam(":expired", htmlspecialchars($timeout), \PDO::PARAM_STR);
            $stmt3->bindParam(":location", htmlspecialchars(json_encode($others['locations'])), \PDO::PARAM_STR);
            $stmt3->bindParam(":details", htmlspecialchars(json_encode($others['device-info'])), \PDO::PARAM_STR);
            $stmt3->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);
            $stmt3->bindParam(":deviceId", htmlspecialchars($deviceId), \PDO::PARAM_STR);

            if ($stmt3->execute()) {
                return true;
            }
        }

        return false;
    }

    private function getPreviousLogIn(string $deviceId, int $userId): bool
    {
        $response = false;

        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `UID` FROM `SOPNOLIKHI_USERS_LOGIN` WHERE `DEVICEID`=:deviceId AND `UID`=:userId LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":deviceId", htmlspecialchars($deviceId), \PDO::PARAM_STR);
            $stmt->bindParam(":userId", htmlspecialchars($userId), \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response = false;
            } else {
                $response = true;
            }


            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }

        return $response;
    }

    private function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
