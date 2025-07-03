<?php
class AdminModel extends Database {
    public function getAdminPassword($email) {
        $query = "SELECT password FROM admin WHERE email='" . $email . "'";
        return mysqli_query($this->connection, $query);
    }

    public function getAdminRole($email) {
        $query = "SELECT role_type FROM admin WHERE email='" . $email . "'";
        return mysqli_query($this->connection, $query);
    }

    public function getAdminId($email) {
        $query = "SELECT id FROM admin WHERE email='" . $email . "'";
        return mysqli_query($this->connection, $query);
    }

    public function getAdminName($id) {
        $query = "SELECT name FROM admin WHERE id='" . $id . "'";
        $result = mysqli_query($this->connection, $query);
        return mysqli_fetch_assoc($result);

    }

    public function checkExistAdmin($email) {
        $query = "SELECT * FROM admin WHERE email='" . $email . "'";
        return mysqli_query($this->connection, $query);
    }

    public function addNewAdmin($name, $email, $password, $avatar, $role, $ins_id) {
        $query = "INSERT INTO admin (name, email, password, avatar, role_type, ins_id, ins_datetime)
                            VALUES (?,?,?,?,?,?, CURRENT_TIMESTAMP)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssssii", $name, $email, $password, $avatar, $role, $ins_id);
        return $stmt->execute();
    }

    public function getAllAdminId($limit, $offset) {
        $query = "SELECT id FROM admin LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii",  $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getFlaggedAdminId($flag, $limit, $offset) {
        $query = "SELECT id FROM admin WHERE del_flag=? LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iii", $flag, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }


    public function getAdminDisplayInfo($id) {
        $query = "SELECT id, name, email, role_type, ins_id, upd_id, del_flag FROM admin WHERE id='" . $id . "'";
        $result = mysqli_query($this->connection, $query);
        return mysqli_fetch_assoc($result);
    }

    public function getTotalAdmin() {
        $query = "SELECT COUNT(*) AS total FROM admin";
        $allRows = mysqli_query($this->connection, $query);
        return mysqli_fetch_assoc($allRows)['total'];
    }

    public function getTotalFilteredAdmin($flag) {
        $query = "SELECT COUNT(*) AS total FROM admin WHERE del_flag='". $flag ."'";
        $allRows = mysqli_query($this->connection, $query);
        return mysqli_fetch_assoc($allRows)['total'];
    }
}