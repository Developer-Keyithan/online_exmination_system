<?php

class UserAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLoggedUserAccesses()
    {
        $role = getLoggedUserRoleID();
        $permissions = getLoggedUserPermissions();
        return json_encode([
            'role' => $role,
            'permissions' => $permissions
        ]);
    }
}