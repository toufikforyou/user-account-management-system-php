<?php

namespace Services\Models\Common;

use Services\Database\SLDatabase;

class AccountCheckModel extends SLDatabase
{

    protected function getUserByUsername(string $username): ?int
    {
        $status = null;

        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            // TODO: SQL
            $sql = 'SELECT `UID` FROM `SOPNOLIKHI_USERS` WHERE `USERNAME` = :username';

            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":username", htmlspecialchars($username), \PDO::PARAM_STR);

            $stmt->execute();
            
            // TODO: Return
            if($stmt->rowCount() > 0)
            {
                $status = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['UID'];
            }
            
            $conn->commit();
        } catch (\PDOException $e) {
            $conn->rollBack();
            
            $status = null;
        }

        return $status;
    }

    protected function getUserByEmail(string $email): ?int
    {
        $status = null;

        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            // TODO: SQL
            $sql = 'SELECT `UID` FROM `SOPNOLIKHI_USERS` WHERE `EMAIL` = :email;';

            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":email", htmlspecialchars($email), \PDO::PARAM_STR);

            $stmt->execute();

            // TODO: Return
            if($stmt->rowCount() > 0)
            {
                $status = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['UID'];
            }
            
            $conn->commit();
        } catch (\PDOException $e) {
            $conn->rollBack();
            
            $status = null;
        }

        return $status;
    }

    protected function getUserByPhone(int $mobile): ?int
    {
        $status = null;

        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            // TODO: SQL
            $sql = 'SELECT `UID` FROM `SOPNOLIKHI_USERS` WHERE `MOBILE` = :mobile';

            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":mobile", htmlspecialchars($mobile), \PDO::PARAM_STR);

            $stmt->execute();
            
            // TODO: Return
            if($stmt->rowCount() > 0)
            {
                $status = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['UID'];
            }

            $conn->commit();
        } catch (\PDOException $e) {
            $conn->rollBack();
            
            $status = null;
        }

        return $status;
    }

    protected function getUserInfo(int $uid): ?array
    {
        $response = null;

        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            // TODO: SQL
            $sql = 'SELECT `SOPNOLIKHI_USERS`.`UID`, `ACTIVE`, `FULLNAME`, `PROFILE`, `MOBILE`, `EMAIL`, `USERNAME`, `GENDER`, `DOB`, `DATE` FROM `SOPNOLIKHI_USERS` INNER JOIN `SOPNOLIKHI_USERS_INFO` ON `SOPNOLIKHI_USERS`.`UID` = `SOPNOLIKHI_USERS_INFO`.`UID` WHERE `SOPNOLIKHI_USERS`.`UID` = :userId LIMIT 1';

            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);

            $stmt->execute();

            if($stmt->rowCount() === 0)
            {
                $conn->commit();
                return array('error' => 1, 'result' => 'User not found!');
            }

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $response = array('success' => 1, 'result' => $result);

            $conn->commit();
        } catch (\PDOException $e) {
            $conn->rollBack();
            
            $response = array("error" => -1, "result" => 'Something is wrong!');
        }

        return $response;
    }
}