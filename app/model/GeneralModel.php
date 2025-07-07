<?php
require_once "./core/helpers.php";
class GeneralModel extends Database {
    public const SQL_NOW = 'CURRENT_TIMESTAMP';

    // Retrieve data
    protected function fetchOneValue($type, $column, $table, $whereCol, $whereVal)
    {
        $query = "SELECT $column FROM $table WHERE $whereCol = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($type, $whereVal);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()[$column] ?? null;
    }

    protected function fetchOneRow($type, $table, $whereCol, $whereVal)
    {
        $query = "SELECT * FROM $table WHERE $whereCol = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($type, $whereVal);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?? null;
    }

    // Insert data
    protected function insertOneRow($table, $pairs)
    {
        $cols = $placeholders = $refs = [];
        $bindTypes = '';

        // Filter types
        foreach ($pairs as $key => $value) {
            $cols[] = $key;
            // Check datetime values
            if ($value === self::SQL_NOW) {
                $placeholders[] = 'CURRENT_TIMESTAMP';
            } else {
                $placeholders[] = '?';
                $bindTypes .= getBindType($value);
                $refs[] = &$pairs[$key];
            }
        }

        // Binding
        $colString = implode(', ', $cols);
        $placeholderString = implode(', ', $placeholders);
        $query = "INSERT INTO $table ($colString) VALUES ($placeholderString)";
        $stmt = $this->connection->prepare($query);

        if (!empty($refs)) {
            array_unshift($refs, $bindTypes);
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed on $table ($colString): " . $stmt->error);
        }
        return true;
    }

    // Update data
    protected function updateMultipleValue($table, $pairs, $id)
    {
        $placeholder = $bindTypes = $newData = '';
        $i = 0;
        $refs = [];

        // Filter types
        foreach ($pairs as $key => $value) {
            // Check datetime values
            if ($value === self::SQL_NOW) {
                $placeholder = 'CURRENT_TIMESTAMP';
            } else {
                $placeholder = '?';
                $bindTypes .= getBindType($value);
                $refs[] = &$pairs[$key];
            }
            $newData .= "$key = $placeholder";
            if ($i < count($pairs) - 1) $newData .= ', ';
            $i++;
        }

        // Append ID
        $bindTypes .= 'i';
        $refs[] = &$id;

        // Binding
        $query = "UPDATE $table SET $newData WHERE id=?";
        $stmt = $this->connection->prepare($query);

        if (!empty($refs)) {
            array_unshift($refs, $bindTypes);
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        return true;
    }

    // Delete data
    protected function deleteOneRow($table, $id)
    {
        $query = "DELETE FROM $table WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return true;
    }

}