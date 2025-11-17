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
        $role = getLoggedUserRoleName();
        $sql = "SELECT * FROM user_group";
        // $sql = "SELECT * FROM user_group WHERE name != 'Technical'";
        // if ($role = 'Administrator') {
        //     $sql .= " AND name != 'Administrator'";
        // } else if ($role = 'Admin') {
        //     $sql .= " AND name != 'Administrator' AND name != 'Admin'";
        // } else {
        //    $sql .= " AND name != 'Administrator' AND name != 'Admin' AND name != 'HOD'";
        // }
        
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

    public function updateUserGroup() {
        $id = $_GET['id'] ?? null;
        $name = $_PUT['group_name'] ?? null;
        $description = $_PUT['group_description'] ?? null;

        if (!$id) {
            return json_encode(['status' => 'error', 'message' => 'Group ID is required.']);
        }

        if (!$name) {
            return json_encode(['status' => 'error', 'message' => 'Group name is required.']);
        }

        $sql = "UPDATE user_group SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $description, $id]);

        return json_encode(['status' => 'success', 'message' => 'User group updated successfully.']);
    }
}