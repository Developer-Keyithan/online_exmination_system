<?php
namespace Backend\Modal;

class User {
    private $table = 'users';
    private $columns = [];
    private $seeds;
    private $db;

    public function __construct() {
        $this->columns = [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255)',
            'phone' => 'VARCHAR(255) UNIQUE NOT NULL',
            'user_group'=> 'INT NOT NULL',
            'email' => 'VARCHAR(255)',
            'password' => 'VARCHAR(255)',
            'status' => 'INT DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ];
        $this->seeds = [
            ['id'=>1,'name' => 'Technical','phone'=>'769104866', 'user_group' => 1, 'email' => 'nit@nit.lk', 'password' => password_hash('@nit',PASSWORD_DEFAULT),'status' => 0],
            ['id'=>2,'name' => 'Super Admin','phone'=>'770000000', 'user_group' => 2, 'email' => 'sadmin@gmail.com', 'password' => password_hash('@nit',PASSWORD_DEFAULT),'status' => 1],
        ];
        $this->db = db();
    }

    // Database table
    public function dbTable() {
        return dbTable($this->table, $this->columns, $this->seeds);
    }

    // check user
    public function checkUser($identifier, $password) {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `phone` = :id OR `email` = :id LIMIT 1");
        $stmt->execute(['id' => $identifier]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Successful login
        }

        return false; // Invalid credentials
    }
}
