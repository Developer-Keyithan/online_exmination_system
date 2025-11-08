<?php
namespace Backend\Modal;

class UserGroup {
    private $table = 'user_group';
    private $columns =[];
    private $seeds;
    public function __construct() {
        $this->columns =[
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255)',
            'permission' => 'TEXT NULL',
            'status' => 'INT DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ];
        $this->seeds = [
            ['id'=>1,'name' => 'Technical'],
            ['id'=>2,'name' => 'Administrator']
        ];
    }
    // Database table
    public function dbTable() {
        return dbTable($this->table, $this->columns, $this->seeds);
    }
    // save data 
    
}
