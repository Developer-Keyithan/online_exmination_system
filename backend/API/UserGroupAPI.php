<?php

class UserGroupAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }

    public function getAllGroups()
    {
        // $sql = "SELECT * FROM user_group";
        $role = getLoggedUserRoleID();
        $sql = "SELECT * FROM user_group WHERE id != 1";
        if ($role == 1) {
            $sql .= "";
        } else if ($role == 2) {
            $sql .= " AND id != 2 AND id != 4 AND id != 7";
        } else if ($role == 3) {
            $sql .= " AND id != 2 AND id != 3 AND id != 4 AND id != 7";
        } else {
           $sql .= " AND id != 2 AND id != 3  AND id != 4 AND id != 7";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groups as &$group) {
            // members count
            $statement = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE user_group = ?");
            $statement->execute([$group['id']]);
            $userCount = $statement->fetch(PDO::FETCH_ASSOC)['count'];
            $group['members_count'] = $userCount;

            // convert permission string to array
            if (!empty($group['permission'])) {
                $permissions = str_replace(["[", "]", "'"], "", $group['permission']); // remove brackets and quotes
                $permissionsArray = array_map('trim', explode(',', $permissions));
                $group['permission'] = $permissionsArray;
            } else {
                $group['permission'] = [];
            }
        }

        return json_encode($groups);
    }

    public function getGroup($id)
    {
        $sql = "SELECT * FROM user_group WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGroupPermissions($id)
    {
        $sql = "SELECT permission FROM user_group WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Convert string "['exams.create']" to proper array
        $permissions = [];
        if (!empty($row['permission'])) {
            $permissions = json_decode(str_replace("'", '"', $row['permission']), true);
        }

        return json_encode(['permissions' => $permissions]);
    }

    public function setPermissions($id)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $permissions = $input['permissions'] ?? [];

        // Convert array to string format "['perm1','perm2']"
        $permissionsString = "['" . implode("','", $permissions) . "']";

        $sql = "UPDATE user_group SET permission = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$permissionsString, $id]);

        return json_encode(['status' => 'success', 'message' => 'Permissions updated successfully.']);
    }

    public function createUserGroup()
    {
        $name = $_POST['group_name'] ?? null;
        $discription = $_POST['group_description'] ?? null;

        if (!$name) {
            return json_encode(['status' => 'error', 'message' => 'Group name is required.']);
        }

        $sql = "INSERT INTO user_group (name, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $discription]);

        return json_encode(['status' => 'success', 'message' => 'User group created successfully.']);
    }

    public function updateUserGroup($id) {
        // $id = $_GET['id'] ?? null;
        if (!$id) {
            return json_encode(['status' => 'error', 'message' => 'Group ID is required.']);
        }

        $name = $_POST['group_name'] ?? null;
        $description = $_POST['group_description'] ?? null;

        if (!$name) {
            return json_encode(['status' => 'error', 'message' => 'Group name is required.']);
        }

        $sql = "UPDATE user_group SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $description, $id]);

        return json_encode(['status' => 'success', 'message' => 'User group updated successfully.']);
    }

    public function deleteUserGroup($id) {
        if (!$id) {
            return json_encode(['status' => 'error', 'message' => 'Group ID is required.']);
        }

        // Check if any users are assigned to this group
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE user_group = ?");
        $stmt->execute([$id]);
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($userCount > 0) {
            return json_encode(['status' => 'error', 'message' => 'Cannot delete group with assigned users.']);
        }

        $sql = "DELETE FROM user_group WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return json_encode(['status' => 'success', 'message' => 'User group deleted successfully.']);
    }
}