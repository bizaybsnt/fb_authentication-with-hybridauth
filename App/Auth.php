<?php

namespace Auth;

use App\Database;

class Auth
{
    private $dObj;

    public function __construct()
    {
        session_start();
        $this->dObj = new Database();
        $this->dObj->connectDb();
    }

    public function logout()
    {
        session_destroy();
        return true;
    }

    public function login($email, $password)
    {
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->cond = array("email" => $email, "password" => $password);
        $count = $this->dObj->totalRows();
        if ($count == 0) {
            $this->dObj->cond = array("social_email" => $email, "password" => $password);
            $count = $this->dObj->totalRows();
        }

        if ($count == 1) {
            $user = $this->dObj->select();
            $_SESSION['userId'] = $user['id'];
            $_SESSION['authUser'] = $user;
            $_SESSION['LoggedIn'] = true;
            $_SESSION['LoggedIn'] = true;
            return true;
        } else {
            $_SESSION['systemMessage'] = "Email/Password does not match!";
            unset($_SESSION['LoggedIn']);
            return false;
        }
    }

    public function find($id)
    {

        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->cond = array("id" => $id);
        $count = $this->dObj->totalRows();
        if ($count == 1) {
            $row = $this->dObj->select();
            return $row;
        }
        return null;
    }

    public function check()
    {
        if (isset($_SESSION['LoggedIn'])) {
            return true;
        } else {
            return false;
        }
    }

    public function redirectTo($url)
    {
        header("Location: " . $url);
        exit;
    }

    public function fbLogin($userProfile)
    {
        $authUser = $this->findOrCreateUser($userProfile, 'facebook');
        if ($authUser) {
            $_SESSION['LoggedIn'] = true;
            $_SESSION['authUser'] = $authUser;
            return true;
        } else {
            return false;
        }
    }

    private function findOrCreateUser($userProfile, $provider)
    {
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->cond = array("provider_id" => $userProfile->identifier);
        $count = $this->dObj->totalRows();

        if ($count == 1) {
            $row = $this->dObj->select();
            return $row;
        }

        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->gate = " OR ";
        $this->dObj->cond = array("email" => $userProfile->email, "social_email" => $userProfile->email);
        $count = $this->dObj->totalRows();

        if ($count != 1) {
            $this->store($userProfile, $provider);
        } else {
            $this->dObj->data = array(
                "provider" => $provider,
                "provider_id" => $userProfile->identifier,
                "avatar" => $userProfile->photoURL
            );
            $this->dObj->update();
        }
        $row = $this->dObj->select();
        return $row;
    }

    private function store($userProfile, $provider)
    {
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->data = array(
            "name" => $userProfile->firstName . " " . $userProfile->lastName,
            "email" => $userProfile->email,
            "provider" => $provider,
            "provider_id" => $userProfile->identifier,
            "avatar" => $userProfile->photoURL
        );
        $this->dObj->insert();
    }

    public function connectFb($userProfile)
    {

        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->cond = array("provider_id" => $userProfile->identifier);
        $count = $this->dObj->totalRows();
        if ($count != 0) {
            $_SESSION['message'] = "Sorry!! This Facebook Account is already associated to other account <br />";
        } else {
            $this->dObj->flushTable();
            $this->dObj->table = "users";
            $this->dObj->cond = array("id" => $_SESSION['authUser']['id']);
            $this->dObj->data = array(
                "social_email" => $userProfile->email,
                "provider" => "facebook",
                "provider_id" => $userProfile->identifier,
                "avatar" => $userProfile->photoURL
            );
            $this->dObj->update();
            $this->login($_SESSION['authUser']['email'], $_SESSION['authUser']['password']);
        }
        return true;
    }

    public function disconnectFb()
    {
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->tableField = "password";
        $this->dObj->cond = array("provider_id" => $_SESSION['authUser']['provider_id']);
        $row = $this->dObj->select();

        if ($row['password'] == null) {
            $_SESSION['systemMessage'] = "Please Enter Password to Disconnect Facebook </br>";
        } else {
            $this->dObj->data = array(
                "provider" => null,
                "provider_id" => null,
                "avatar" => null
            );
            unset($_SESSION['HYBRIDAUTH::STORAGE']);
            $this->dObj->update();
        }
    }

    public function checkFbStatus($provider_id)
    {
        $this->dObj->flushTable();
        $this->dObj->table = 'users';
        $this->dObj->cond = array("provider_id" => $provider_id);
        $data = $this->dObj->select();
        return $data;
    }

    public function register($details)
    {
        if ($this->checkConfirmPassword($details['password'], $details['confirm_password']) == false) {
            return false;
        }
        if ($this->checkEmail($details['email']) == false) {
            return false;
        }
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->data = array(
            "name" => $details['name'],
            "password" => $details['password'],
            "email" => $details['email']
        );
        $this->dObj->insert();
        return true;
    }

    private function checkConfirmPassword($pass, $confirmPass)
    {
        if ($pass == '') {
            $_SESSION['systemMessage'] = "Please Enter Password </br>";
            return false;
        }
        if ($pass != $confirmPass) {
            $_SESSION['systemMessage'] = "Password did not match confirm Password </br>";
            return false;
        } else {
            return true;
        }
    }

    private function checkEmail($email)
    {
        $this->dObj->flushTable();
        $this->dObj->table = "users";
        $this->dObj->cond = array("email" => $email);
        $count = $this->dObj->totalRows();
        if ($count == 1) {
            $_SESSION['systemMessage'] = "This Email is already used";
            return false;
        } else {
            $this->dObj->flushTable();
            $this->dObj->table = "users";
            $this->dObj->cond = array("social_email" => $email);
            $count = $this->dObj->totalRows();
            if ($count == 1) {
                $_SESSION['systemMessage'] = "This Email is already used";
                return false;
            }
        }
        return true;
    }

    public function createPassword($password, $confirmPassword)
    {
        if ($this->checkConfirmPassword($password, $confirmPassword) == false) {
            return false;
        } else {
            $this->dObj->flushTable();
            $this->dObj->table = "users";
            $this->dObj->cond = array("id" => $_SESSION['authUser']['id']);
            $this->dObj->data = array(
                "password" => $password
            );
            $this->dObj->update();
            $_SESSION['authUser']['password'] = $password;
            $_SESSION['systemMessage'] = "Password Created Successfully <br />";
            return true;
        }
    }
}
