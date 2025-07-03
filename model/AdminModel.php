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
}