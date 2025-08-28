<?php
require_once 'config.php';
include_once 'session.php';
include 'database.php';

class Core
{
    private $db;
    private $table;
    private $select;
    private $join;
    private $conditions;
    private $orderBy;
    private $limit;
    private $bindings;
    private $updateValues;
    private $groupBy = [];

    private $error_trigger;

    public function __construct()
    {
        $this->db = new Database();
        $this->resetQuery();
    }

    private function resetQuery()
    {
        $this->table = null;
        $this->select = '*';
        $this->join = [];
        $this->conditions = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->bindings = [];
        $this->updateValues = [];
        $this->groupBy = [];
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns = '*')
    {
        if (is_array($columns)) {
            $this->select = implode(', ', $columns);
        } else {
            $this->select = $columns;
        }
        return $this;
    }

    public function innerJoin($table, $condition)
    {
        $this->join[] = "INNER JOIN {$table} ON {$condition}";
        return $this;
    }

    public function leftJoin($table, $condition)
    {
        $this->join[] = "LEFT JOIN {$table} ON {$condition}";
        return $this;
    }

    public function crossJoin($table, $condition)
    {
        $this->join[] = "CROSS JOIN {$table} ON {$condition}";
        return $this;
    }

    public function where($column, $value, $operator = '=')
    {
        $this->conditions[] = ["type" => "where", "column" => $column, "value" => $value, "operator" => $operator];
        $this->bindings[":{$column}"] = $value;
        return $this;
    }

    public function orWhere($column, $value, $operator = '=')
    {
        $this->conditions[] = ["type" => "orWhere", "column" => $column, "value" => $value, "operator" => $operator];
        $this->bindings[":{$column}"] = $value;
        return $this;
    }

    public function whereIn($column, array $values)
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->conditions[] = ["type" => "whereIn", "column" => $column, "values" => $values, "placeholders" => $placeholders];
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function like($column, $value)
    {
        $this->conditions[] = ["type" => "like", "column" => $column, "value" => "%{$value}%"];
        $this->bindings[":{$column}"] = "%{$value}%";
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->limit = "{$offset}, {$limit}";
        return $this;
    }

    public function groupBy($columns)
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $this->groupBy = $columns;
        return $this; // Return $this for method chaining
    }

    public function update(array $values)
    {
        $this->updateValues = $values;
        return $this;
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . $this->buildConditions();
        }

        $affectedRows = $this->executeQuery($sql, $this->bindings);
        $this->resetQuery();
        return $affectedRows;
    }

    public function save()
    {
        if (empty($this->updateValues)) {
            throw new Exception('No values to update');
        }

        $setParts = [];
        foreach ($this->updateValues as $column => $value) {
            $setParts[] = "{$column} = :{$column}";
            $this->bindings[":{$column}"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . $this->buildConditions();
        }

        $affectedRows = $this->executeQuery($sql, $this->bindings);
        $this->resetQuery();
        return $affectedRows;
    }

    public function get()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->join)) {
            $sql .= ' ' . implode(' ', $this->join);
        }

        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . $this->buildConditions();
        }

        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if (!empty($this->limit)) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        $result = $this->executeQuery($sql, $this->bindings, true);
        $this->resetQuery();
        return $result;
    }

    public function insert(array $data, string $redirect = null)
    {
        if (empty($this->table)) {
            return $this->toast("error", "Table not found");
        }

        if (empty($data)) {
            return $this->toast("error", "Insert data is empty.");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
        $this->bindings = array_combine(
            array_map(fn($key) => ":$key", array_keys($data)),
            array_values($data)
        );

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        // Execute the query using executeQuery
        $affectedRows = $this->executeQuery($sql, $this->bindings);

        // Reset the query state
        $this->resetQuery();

        if ($redirect != null) {
            return $this->es_redirect($redirect);
        }

        return $affectedRows; // Return the number of affected rows
    }

    private function buildConditions()
    {
        $sql = '';
        foreach ($this->conditions as $index => $condition) {
            if ($condition["type"] === "where") {
                $sql .= ($index > 0 ? ' AND ' : '') . "{$condition['column']} {$condition['operator']} :{$condition['column']}";
            } elseif ($condition["type"] === "orWhere") {
                $sql .= ($index > 0 ? ' OR ' : '') . "{$condition['column']} {$condition['operator']} :{$condition['column']}";
            } elseif ($condition["type"] === "whereIn") {
                $sql .= ($index > 0 ? ' AND ' : '') . "{$condition['column']} IN ({$condition['placeholders']})";
            } elseif ($condition["type"] === "like") {
                $sql .= ($index > 0 ? ' AND ' : '') . "{$condition['column']} LIKE :{$condition['column']}";
            }
        }
        return $sql;
    }

    private function executeQuery($sql, $bindings = [], $fetchAll = false)
    {
        $query = $this->db->pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $query->bindValue($key, $value);
        }
        $query->execute();

        if ($fetchAll) {
            return $query->fetchAll(PDO::FETCH_OBJ); // Return all results for SELECT queries
        } else {
            return $query->rowCount(); // Return affected rows count for UPDATE and DELETE queries
        }
    }

    // ====================================================
    // Helper Functions
    // ====================================================

    function trigger_error_message(
        string $message,
        string $containerId = 'default',
        string $type = 'error',
        bool $dismissible = true
    ) {
        $this->error_trigger->trigger($message, $containerId, $type, $dismissible);
    }

    public function toast($type, $massage)
    {
        if ($type == 'error') {
            return '<div class="bg-red-100 text-red-700 py-2 px-4 rounded-lg w-full md:w-1/2">' . $massage . '</div>';
        } elseif ($type == 'warning') {
            return '<div class="bg-yellow-100 text-yellow-700 py-2 px-4 rounded-lg w-full md:w-1/2">' . $massage . '</div>';
        } else {
            return '<div class="bg-green-100 text-green-700 py-2 px-4 rounded-lg w-full md:w-1/2">' . $massage . '</div>';
        }
    }

    public function es_redirect($file_name)
    {
        return header('location:' . SITE_URL . '/' . $file_name);
    }

    public function separateByHyphen($input)
    {
        return implode('-', str_split($input, 4));
    }

    public function dd($data)
    {
        echo '<pre class="bg-gray-900 text-gray-300 p-4 max-h-[400px] font-xs font-bold overflow-x-scroll">';
        echo (var_dump($data));
        echo '</pre>';
    }

    public function noData($data)
    {
        if (count($data) == 0) {
            return '<div class="div-normal-border">
    <div class="div-normal border-b-2 border-emon-accent">
        There is no data found.
    </div>
</div>';
        }
    }

    public function uuid($length = 32)
    {
        $randomBytes = random_bytes(ceil($length * 3 / 4));
        $base64String = rtrim(strtr(base64_encode($randomBytes), '+/', 'Aa'), '=');
        return substr($base64String, 0, $length);
    }

    public function validateHash($table, $hash)
    {
        $result = $this->table($table)->where('uid', $hash)->get();
        if (count($result) <= 0) {
            $this->es_redirect('404.php');
        }
    }

    public function deleteFilesRecursively($directory)
    {
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $directory . '/' . $file;
                if (is_dir($filePath)) {
                    $this->deleteFilesRecursively($filePath); // Recursive call for subdirectories
                    rmdir($filePath); // Delete the empty directory
                } else {
                    unlink($filePath);
                }
            }
        }
    }

    public function getReadableFileSize($filePath)
    {
        if (!file_exists($filePath)) {
            return "File does not exist.";
        }

        $size = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function formatNumber($number)
    {
        $formattedNumber = number_format($number, 0, '', ' ');

        return $formattedNumber;
    }

    public function updatePosition($table, $currentPosition, $newPosition)
    {
        $sql = "SELECT * FROM $table WHERE position = :position";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':position', $currentPosition);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($currentPosition != $newPosition) {
            // Adjust positions of other rows
            if ($newPosition < $currentPosition) {
                $updateQuery = "UPDATE $table SET position = position + 1 WHERE position >= ? AND position < ?";
                $stmt = $this->db->pdo->prepare($updateQuery);
                $stmt->bindParam(1, $newPosition);
                $stmt->bindParam(2, $currentPosition);
                $stmt->execute();
            } else {
                $updateQuery = "UPDATE $table SET position = position - 1 WHERE position <= ? AND position > ?";
                $stmt = $this->db->pdo->prepare($updateQuery);
                $stmt->bindParam(1, $newPosition);
                $stmt->bindParam(2, $currentPosition);
                $stmt->execute();
            }

            // Update the position of the current user
            $query = "UPDATE $table SET position = ? WHERE id = ?";
            $stmt = $this->db->pdo->prepare($query);
            $stmt->bindParam(1, $newPosition);
            $stmt->bindParam(2, $result->id);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }


    //=====================================================
    // Encoder
    //=====================================================
    public function es_encrypt(string $input): string
    {
        $key = hash('sha256', ENCRYPTION_KEY, true); // Generate a 256-bit key
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD)); // Generate a random IV
        $encrypted = openssl_encrypt($input, ENCRYPTION_METHOD, $key, 0, $iv);

        // Combine IV and encrypted string for storage
        $base64Encoded = base64_encode($iv . $encrypted);

        // Convert base64 to a URL-safe base64 variant
        return strtr($base64Encoded, '+/', '-_');
    }

    // Function to decrypt an encrypted string
    public function es_decrypt(string $encryptedInput): string
    {
        $key = hash('sha256', ENCRYPTION_KEY, true); // Generate the same 256-bit key

        // Convert URL-safe base64 back to standard base64
        $base64Encoded = strtr($encryptedInput, '-_', '+/');
        $data = base64_decode($base64Encoded); // Decode the base64 string

        $ivLength = openssl_cipher_iv_length(ENCRYPTION_METHOD);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        return openssl_decrypt($encrypted, ENCRYPTION_METHOD, $key, 0, $iv);
    }

    // ====================================================
    // Global Variables
    // ====================================================

    public function systemOptionUpdate($var_name, $var_value)
    {
        $sql = "UPDATE global_variable SET var_value = :var_value WHERE var_name = :var_name";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':var_name', $var_name);
        $query->bindValue(':var_value', $var_value);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function systemOption($var_name)
    {
        $sql = "SELECT * FROM global_variable WHERE var_name = :var_name";
        $query = $this->db->pdo->prepare($sql);
        $query->bindValue(':var_name', $var_name);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    //==========================================
    // System Login Functions
    //==========================================

    public function adminLogin($data)
    {
        $username = $data['username'];
        $input_password = $data['password'];
        $password = md5($input_password);

        $chkUser = $this->table('admin')->where('username', $username)->get();

        if ($chkUser != true) {
            return $this->toast('error', 'User not found.');
        }

        $user = $this->table("admin")->where('username', $username)->where("password", $password)->get();

        if (count($user) > 0) {
            session::init();
            session::set("login", true);
            session::set("isAdmin", true);
            session::set("username", 'admin');
            session::set("whoIs", 'admin');

            return $this->es_redirect('adm/dashboard.php');
        } else {
            $msg = $this->toast("error", "Wrong credentials.");
            return $msg;
        }
    }
}
