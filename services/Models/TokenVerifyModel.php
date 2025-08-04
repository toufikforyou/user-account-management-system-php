<?php

namespace Services\Models;

use Services\Database\SLDatabase;

class TokenVerifyModel extends SLDatabase
{
    protected function tokenVerify(int $uid, string $token): ?array
    {
        $response = null;

        try {
            //code...
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `UID`, `TOKEN`, `DEVICEID`, `EXPIRED` FROM `SOPNOLIKHI_USERS_LOGIN` WHERE  `UID`=:userId AND `TOKEN`=:token LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);
            $stmt->bindParam(":token", htmlspecialchars($token), \PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result['EXPIRED'] > time()) {
                $response = array(
                    'success' => 1,
                    'result' => [
                        'uid' => $result['UID'],
                        'token' => $result['TOKEN'],
                        'device' => $result['DEVICEID'],
                        'expired' => date("Y-m-d H:i:s", $result['EXPIRED'])
                    ]
                );
            } else {
                $response = array('error' => 1, 'result' => 'Login token has been expired');
            }


            $conn->commit();
        } catch (\Exception $e) {
            //throw $th;
            $conn->rollBack();

            $response = array('error' => -1, 'result' => 'Something is wrong please try agin later!');
        }

        return $response;
    }
}
