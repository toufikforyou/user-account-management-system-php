<?php
namespace Services\Models;

use Services\Database\SLDatabase;

class SignUpModel extends SLDatabase
{
    /**
     * User create account model
     * Response 
     * $success = array('success' => 1,
     * 'result' => [
     *      'uid' => $userId,
     *      'full-name' => htmlspecialchars($user['full-name']),
     *      'email' => htmlspecialchars($email),
     *      'mobile' => htmlspecialchars($mobile)
     *  ]);
     * 
     * Response
     * $Error Default = array('error' => -1, 'result' => 'Something is wrong please try agin later!');
    */

    protected function createUserAccount(array $user): ?array
    {
        $result = array();

        // Last user id fetch in the database user table;
        $userId = $this->lastUID();

        // User password hash for strong to save in the database.
        $password = password_hash($user['password'], PASSWORD_DEFAULT);
        
        // mobail, email, username, nid is uniqe that why it is empty then replace to uid
        $mobile = empty($user['mobile']) || $user['mobile'] === 0 ? $userId : (int) $user['mobile'];
        $email = empty($user['email']) ? $userId : $user['email'];
        $username = $userId;

        $nid = empty($user['nid-number']) || $user['nid-number'] === 0 ? $userId : $user['nid-number'];

        date("Y-m-d H:i:s", time());

        $dob = $user['date-of-birth'] ? $user['date-of-birth'] : "0000-00-00";

        try{
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'INSERT INTO `SOPNOLIKHI_USERS`(`UID`, `FULLNAME`, `MOBILE`, `EMAIL`, `USERNAME`, `PASSWORD`)
                    VALUES (:userId, :fullName, :mobile, :email, :username, :pwd);';

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":userId", htmlspecialchars($userId), \PDO::PARAM_STR);
            $stmt->bindParam(":fullName", htmlspecialchars($user['full-name']), \PDO::PARAM_STR);
            $stmt->bindParam(":mobile", htmlspecialchars($mobile), \PDO::PARAM_STR);
            $stmt->bindParam(":email", htmlspecialchars($email), \PDO::PARAM_STR);
            $stmt->bindParam(":username", htmlspecialchars($username), \PDO::PARAM_STR);
            $stmt->bindParam(":pwd", htmlspecialchars($password), \PDO::PARAM_STR);

            $stmt->execute();
            
            $sql2 = 'INSERT INTO `SOPNOLIKHI_USERS_INFO`(`UID`, `GENDER`, `DOB`, `NID`, `FNAME`, `MNAME`, `ADDRESS`, `LOCATION`, `DETAILS`)
                    VALUES (:userId, :gender, :dob, :nid, :fName, :mName, :address, :locations, :details);';
            
            $stmt2 = $conn->prepare($sql2);

            
            $stmt2->bindParam(":userId", htmlspecialchars($userId), \PDO::PARAM_STR);
            $stmt2->bindParam(":gender", htmlspecialchars($user['gender']), \PDO::PARAM_INT);
            $stmt2->bindParam(":dob", htmlspecialchars($dob), \PDO::PARAM_STR);
            $stmt2->bindParam(":nid", htmlspecialchars($nid), \PDO::PARAM_STR);
            $stmt2->bindParam(":fName", htmlspecialchars($user['father-name']), \PDO::PARAM_STR);
            $stmt2->bindParam(":mName", htmlspecialchars($user['mother-name']), \PDO::PARAM_STR);
            $stmt2->bindParam(":address", htmlspecialchars(json_encode($user['address'])), \PDO::PARAM_STR);
            $stmt2->bindParam(":locations", htmlspecialchars(json_encode($user['locations'])), \PDO::PARAM_STR);
            $stmt2->bindParam(":details", htmlspecialchars(json_encode($user['details'])), \PDO::PARAM_STR);

            $stmt2->execute();

            $result = array('success' => 1,
                'result' => [
                    'uid' => $userId,
                    'full-name' => htmlspecialchars($user['full-name']),
                    'email' => htmlspecialchars($email),
                    'mobile' => htmlspecialchars($mobile)
                ]
            );

            $conn->commit();
        }catch(\Exception $e)
        {
            $conn->rollBack();
            $result = array('error' => -1, 'result' => 'Something is wrong please try agin later!');
        }
        
        return $result;
    }


    /**
     * User create account model
     * Response 
     * $Success = int $uid, by default uid is 202308815010000
     * 
     * Response
     * $Error = int $uid = 0;
    */
    private function lastUID(): int
    {
        $uid = 202308815010000;

        try {
            $conn = $this->connect();
            $conn->beginTransaction();

            $sql = 'SELECT `UID` FROM `SOPNOLIKHI_USERS` ORDER BY `UID` DESC LIMIT 1;';

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            // TODO:: Return UID or user id (int)
            $uid = $result ? (int) $result['UID'] + 1 : $uid;

            $conn->commit();
            
        } catch (\PDOException $e) {
            $conn->rollBack();

            $uid = 0;
        }

        return $uid;
    }
}