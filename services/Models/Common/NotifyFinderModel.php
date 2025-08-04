<?php
namespace Services\Models\Common;

use Services\Database\SLDatabase;

class NotifyFinderModel extends SLDatabase
{
    public function getEmail(int $uid): string
    {
        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `EMAIL` FROM `SOPNOLIKHI_USERS` WHERE `UID`=:userId LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC)['EMAIL'];

            $conn->commit();
        }catch(\Exception $e)
        {
            $conn->rollBack();

            $result = '';
        }

        return $result;
    }

    public function getMobile(int $uid): string
    {
        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `MOBILE` FROM `SOPNOLIKHI_USERS` WHERE `UID`=:userId LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", htmlspecialchars($uid), \PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC)['MOBILE'];


            $conn->commit();
        }catch(\Exception $e)
        {
            $conn->rollBack();

            $result = '';
        }

        return $result;
    }
}