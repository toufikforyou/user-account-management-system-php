<?php

namespace Includes\Common;

class Validation
{
    /**
     *    @category Validation for mobail number;
     *    @category Mobail number with country code;
     */

    public static function formatPhoneNumber(string $mobile): string
    {
        $mobile = preg_replace('/\D/', '', $mobile);

        $formatNumber = '880' . ltrim($mobile, '880');

        return self::validateMobile($mobile) ? $formatNumber : '0';
    }

    /**
     *    @category Validation for mobail number;
     *    @category Mobail number validation check return true or false;
     */

    public static function validateMobile(string $mobail): bool
    {
        $pattern = '/^(\\+?8801|01|8801|1)[1-9][0-9]{8}$/';

        return is_numeric($mobail) && preg_match($pattern, $mobail);
    }
    /**
     *    @category Validation for email account;
     *    @category Email Account validation check return true or false;
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    /**
     *    @category Validation for user username;
     *    @category Username validation check return true or false;
     */
    public static function validateUsername(string $username): bool
    {
        $pattern = '/^[a-zA-Z0-9._]{5,20}$/';

        return preg_match($pattern, $username);
    }

    public static function validateUid(int $uid): bool
    {
        return $uid >= 202308815010000;
    }
    /**
     *    @category Validation for user create password;
     *    @category password is valid then return true or false;
     */
    public static function validatePassword(string $password): bool
    {
        return is_string($password);
    }
    public static function validatePassword2(string $password): bool
    {
        $pattern = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#&()–{}:;\',?\/\*~$^+=<>]).{8,20}$/";

        return preg_match($pattern, $password);
    }

    public static function checkDateFormat(string $value, string $format = 'Y-m-d'): bool
    {
            $date = \DateTime::createFromFormat($format, $value); // Create a DateTime object
            return $date && $date->format($format) === $value; // Compare the formatted value with the
    }
}