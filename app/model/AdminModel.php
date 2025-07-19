<?php
require_once "GeneralModel.php";
class AdminModel extends GeneralModel
{
    public function checkExistEmail($table, $email)
    {
        if ($this->fetchOneRow($table, "email", $email, "s")) {
            return true;
        } else {
            return false;
        }
    }
    public function getPersonPassword($table, $email)
    {
        return $this->fetchOneValue($table, "password", "email", $email, "s");
    }

    public function getPersonId($table, $email)
    {
        return (int)$this->fetchOneValue($table, "id", "email", $email, "s");
    }

    public function getPersonInfo($table, $id)
    {
        return $this->fetchOneRow($table, "id", $id, "i");
    }

    public function getMultipleFlaggedPeople($table, $limit, $offset, $flag = null, $searchby = null, $searchpattern = null)
    {
        $validCols = ['name', 'email'];
        $query = $bindTypes = '';
        $params = [];
        if ($flag === null) {
            if ($searchby !== null && $searchpattern !== null && in_array($searchby, $validCols)) {
                $query = "SELECT * FROM `$table` WHERE `$searchby` LIKE ? LIMIT ? OFFSET ?";
                $bindTypes = "sii";
                $params = ["%$searchpattern%", $limit, $offset];
            } else {
                $query = "SELECT * FROM `$table` LIMIT ? OFFSET ?";
                $bindTypes = "ii";
                $params = [$limit, $offset];
            }
        } else {
            if ($searchby !== null && $searchpattern !== null && in_array($searchby, $validCols)) {
                $query = "SELECT * FROM `$table` WHERE del_flag=? AND `$searchby` LIKE ? LIMIT ? OFFSET ?";
                $bindTypes = "isii";
                $params = [$flag, "%$searchpattern%", $limit, $offset];
            } else {
                $query = "SELECT * FROM `$table` WHERE del_flag=? LIMIT ? OFFSET ?";
                $bindTypes = "iii";
                $params = [$flag, $limit, $offset];
            }
        }
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($bindTypes, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalFlaggedPeople($table, $flag = null, $searchby = null, $searchpattern = null)
    {
        $validCols = ['name', 'email'];
        $pattern = "%$searchpattern%";
        if ($flag === null) {
            if ($searchby !== null && $searchpattern !== null && in_array($searchby, $validCols)) {
                $query = "SELECT COUNT(*) AS total FROM `$table` WHERE `$searchby` LIKE ?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("s", $pattern);
                $stmt->execute();
                $total = $stmt->get_result()->fetch_assoc()['total'];
            } else {
                $query = "SELECT COUNT(*) AS total FROM `$table`";
                $total = $this->connection->query($query)->fetch_assoc()['total'];
            }
        } else {
            if ($searchby !== null && $searchpattern !== null && in_array($searchby, $validCols)) {
                $query = "SELECT COUNT(*) AS total FROM `$table` WHERE del_flag=? AND `$searchby` LIKE ?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("is", $flag, $pattern);
                $stmt->execute();
                $total = $stmt->get_result()->fetch_assoc()['total'];
            } else {
                $query = "SELECT COUNT(*) AS total FROM `$table` WHERE del_flag=?";
                $stmt = $this->connection->prepare($query);
                $stmt->bind_param("i", $flag);
                $stmt->execute();
                $total = $stmt->get_result()->fetch_assoc()['total'];
            }
        }

        return $total;
    }

    public function addNewPerson($table, $insertData)
    {
        try {
            return $this->insertOneRow($table, $insertData);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updatePerson($table, $id, $pairs)
    {
        try {
            return $this->updateMultipleValue($table, $id, $pairs);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function softDeletePerson($table, $id)
    {
        return $this->updatePerson($table, $id, ['del_flag' => 1]);
    }

    public function hardDeletePerson($table, $id)
    {
        return $this->deleteOneRow($table, $id);
    }

    public function recoverDeletedPerson($table, $id)
    {
        return $this->updatePerson($table, $id, ['del_flag' => 0]);
    }

}