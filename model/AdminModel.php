<?php
require_once "GeneralModel.php";
class AdminModel extends GeneralModel {

    public function getAdminPassword($email) {
        return $this->fetchOneValue("s", "password", "admin", "email", $email);
    }
    public function getAdminId($email) {
        return (int) $this->fetchOneValue("s", "id", "admin", "email", $email);
    }

    public function checkExistAdminEmail($email) {
        return $this->fetchOneRow("s", "admin", "email", $email);
    }


    public function getAdminInfo($id) {
        return $this->fetchOneRow("i", "admin", "id", $id);
    }

    public function getMultipleFlaggedAdmin($limit, $offset, $flag = null) {
        $query = "SELECT * FROM admin WHERE del_flag=? LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iii", $flag, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalFlaggedAdmin($flag) {
        $query = "SELECT COUNT(*) AS total FROM admin WHERE del_flag=?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $flag);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public function addNewAdmin($insertData) {
        try {
            return $this->insertOneRow('admin', $insertData);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updateAdmin($pairs, $id) {
        try {
            return $this->updateMultipleValue('admin', $pairs, $id);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function softDeleteAdmin($id) {
        return $this->updateAdmin(['del_flag' => 1], $id);
    }

    public function hardDeleteAdmin($id) {
        return $this->deleteOneRow('admin', $id);
    }

    public function recoverDeletedAdmin($id) {
        return $this->updateAdmin(['del_flag' => 0], $id);
    }

}