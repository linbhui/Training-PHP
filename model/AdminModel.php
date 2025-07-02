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
}