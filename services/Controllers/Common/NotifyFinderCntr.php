<?php
namespace Services\Controllers\Common;

use Services\Models\Common\NotifyFinderModel;

class NotifyFinderCntr extends NotifyFinderModel
{

    public static function getEmailId(int $uid): string
    {
        $instance = new self(); // Create an instance of the class
        return $instance->getEmailAccountId($uid);
    }

    private function getEmailAccountId(int $uid): string
    {
        return $this->getEmail($uid);
    }


    public static function getMobileId(int $uid): string
    {
        $instance = new self(); // Create an instance of the class
        return $instance->getMobileAccountId($uid);
    }


    private function getMobileAccountId(int $uid): string
    {
        return $this->getMobile($uid);
    }

}