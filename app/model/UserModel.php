<?php
require_once "GeneralModel.php";

class UserModel extends GeneralModel
{
    public function checkExistUserEmail($email)
    {
        if ($this->fetchOneRow("user", "email", $email, "s")) {
            return true;
        } else {
            return false;
        }
    }
    public function getUserPassword($email)
    {
        return $this->fetchOneValue("user", "password", "email", $email, "s");
    }

    public function getUserId($email)
    {
        return (int)$this->fetchOneValue("user", "id", "email", $email, "s");
    }

    public function getUserInfo($id)
    {
        return $this->fetchOneRow("user", "id", $id, "i");
    }

    public function setUserOnline($id) {
        try {
            return $this->updateMultipleValue('user', $id, ['status' => 1]);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function setUserOffline($id) {
        try {
            return $this->updateMultipleValue('user', $id, ['status' => 2]);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}