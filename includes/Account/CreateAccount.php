<?php

namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\UserCheck;
use Includes\Common\Validation;
use Services\Controllers\SignUpCntr;

class CreateAccount extends SignUpCntr
{
    private string $fullName;
    private string $profile;
    private string $account;
    private string $email;
    private string $username;
    private string $password;
    private string $gender;
    private string $dateOfBirth;
    private int $nidNumber;
    private string $fatherName;
    private string $motherName;

    private string $address;

    private string $country, $division, $district, $upazila, $union, $village;

    private $locations;

    private $details;


    public function __construct($params)
    {
        $this->fullName = trim($params['full-name']);
        $this->profile = trim($params['profile']);
        $this->account = trim($params['account']);
        $this->password = trim($params['password']);
        $this->gender = trim($params['gender']);
        $this->dateOfBirth = trim($params['date-of-birth']);
        $this->nidNumber = (int) trim($params['nid-number']);
        $this->fatherName = trim($params['father-name']);
        $this->motherName = trim($params['mother-name']);
        
        $this->address = trim($params['address']);
        $this->country = trim($params['country']);
        $this->division = trim($params['division']);
        $this->district = trim($params['district']);
        $this->upazila = trim($params['upazila']);
        $this->union = trim($params['union']);
        $this->village = trim($params['village']);

        $this->locations = $params['locations'];

        $this->details = $params['device-info'];
    }

    public function account(): string
    {
        if(!Validation::validateEmail($this->account) && !Validation::validateMobile($this->account))
        {
            http_response_code(400);
            return ReturnSmg::return(1050, false, '701: Email address or mobile is invalid!');
        }

        if (!Validation::validatePassword($this->password))
        {
            http_response_code(400);

            return ReturnSmg::return(1050, false, '705: Password empty or invalid!');
        }

        if(!empty($this->dateOfBirth) && !Validation::checkDateFormat($this->dateOfBirth))
        {
            http_response_code(400);

            return ReturnSmg::return(1050, false, '706: Invalid date of birth!');
        }
        
        if (!UserCheck::checkUser($this->account, $this->account, Validation::formatPhoneNumber($this->account)))
        {
            http_response_code(400);

            return ReturnSmg::return(1051, false, '701: User already exists!');
        }

        return $this->createAccountRequest();
    }


    private function createAccountRequest(): string
    {
        return $this->createAccount($this->userArray());
    }

    private function userArray(): array
    {
        return array(
            'full-name' => $this->fullName,
            'profile' => $this->profile,
            'mobile' => is_numeric($this->account) ? (int) Validation::formatPhoneNumber($this->account) : 0,
            'email' => Validation::validateEmail($this->account) ? $this->account : '',
            'password' => md5(htmlspecialchars($this->password)),
            'gender' => $this->gender,
            'date-of-birth' => $this->dateOfBirth,
            'nid-number' => $this->nidNumber,
            'father-name' => $this->fatherName,
            'mother-name' => $this->motherName,
            'address' => [
                'country' => $this->country,
                'division' => $this->division,
                'district' => $this->district,
                'upazila' => $this->upazila,
                'union' => $this->union,
                'village' => $this->village,
                'address' => $this->address
            ],
            'locations' => $this->locations,
            'details' => [
                'device-info' => $this->details,
                'ip-address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
    }


}