<?php $this->extend('frontend');
$this->controller('UserController');
$user = user_id(); // Logged-in user

$sql = "SELECT id, name, 
            (SELECT COUNT(*) FROM users WHERE user_group = ug.id";

// Apply role-based filtering for counting users
if ($user == 1) {
    $sql .= " AND user_group != 1";
} elseif ($user == 2) {
    $sql .= " AND user_group NOT IN (1,2,4,7)";
} else {
    $sql .= " AND user_group NOT IN (1,2,3,4,7)";
}

// Close subquery
$sql .= ") AS user_count 
         FROM user_group ug";

// Apply role-based filtering for visible groups
if ($user == 1) {
    $sql .= " WHERE id != 1";
} elseif ($user == 2) {
    $sql .= " WHERE id NOT IN (1,2,4,7)";
} else {
    $sql .= " WHERE id NOT IN (1,2,3,4,7)";
}

$stmt = db()->prepare($sql);
$stmt->execute();
$userGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php $this->start('content'); ?>

<div class="bg-[#0003] p-6 rounded-lg mb-16">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-gray-400">Manage system users and their accounts</p>
        </div>
        <a href="add_user"
            class="bg-cyan-600 hover:bg-cyan-700 mt-4 md:mt-0 w-fit text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-user-plus"></i>
            <span>Add User</span>
        </a>
    </div>

    <!-- Filters and Search -->
    <div ng-cloak class="mb-6">
        <div class="flex flex-wrap lg:flex-row gap-4 items-center">
            <!-- Group Filter -->
            <select ng-model="selectedGroup" ng-change="filterUsers()" ng-disabled="isLoading"
                class="select2 w-96 md:w-[22rem]" placeholder="Select a group">
                <option value="">All Groups</option>
                <?php foreach ($userGroups as $group): ?>
                    <option value="<?= $group['id'] ?>">
                        <?= $group['name'] ?> (<?= $group['user_count'] ?> users)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Status Filter -->
            <select ng-model="selectedStatus" ng-change="filterUsers()" class="select2 w-96 md:w-[22rem]">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>

            <!-- Export Button -->
            <!-- <button
                class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button> -->

        </div>
    </div>

    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading Users...</h3>
            <p class="text-gray-400">Please wait while we fetch user data.</p>
        </div>
    </div>

    <!-- Users Table -->
    <div ng-cloak ng-if="!loading && filteredUsers.length > 0" class="">
        <div class="overflow-x-auto">
            <div class="flex flex-wrap mb-4 items-center justify-center md:justify-between pt-2">
                <div class="flex items-center space-x-2">
                    <span class="text-gray-400 text-sm">Show</span>
                    <select ng-model="pageSize" ng-change="changePageSize()" id="pageSizeSelect"
                        class="bg-transparent border border-gray-600 rounded px-2 py-1 text-gray-400">
                        <option ng-repeat="size in pageSizeOptions" value="{{size}}">{{size}}</option>
                    </select>
                    <span class="text-gray-400 text-sm">users per page</span>
                </div>
                <!-- Search -->
                <div class="flex-1 min-w-[200px] max-w-96">
                    <div class="relative">
                        <input type="text" ng-model="searchTerm" ng-change="filterUsers()" style="padding-left: 2.5rem;"
                            placeholder="Search users by name, or email..." class="bg-[#fff1] w-full">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full bg-[#0004] rounded-lg overflow-hidden">
                    <thead class="bg-[#0006] border-b border-[#fff2]">
                        <tr>
                            <!-- <th class="py-3 px-4 text-left font-semibold text-gray-300">
                            <input type="checkbox" class="rounded bg-[#0006] border-[#fff2]">
                        </th> -->
                            <th class="py-3 px-4 text-left font-semibold text-gray-300 cursor-pointer"
                                ng-click="sortBy('name')">
                                User
                                <i ng-class="{'fas fa-sort-up': sortColumn === 'name' && !sortReverse,
                                'fas fa-sort-down': sortColumn === 'name' && sortReverse,
                                'fas fa-sort': sortColumn !== 'name'}" class="text-xs"></i>
                            </th>

                            <th class="py-3 px-4 text-left font-semibold text-gray-300 cursor-pointer"
                                ng-click="sortBy('group_name')">
                                Group
                                <i ng-class="{'fas fa-sort-up': sortColumn === 'group_name' && !sortReverse,
                                'fas fa-sort-down': sortColumn === 'group_name' && sortReverse,
                                'fas fa-sort': sortColumn !== 'group_name'}" class="text-xs"></i>
                            </th>

                            <th class="py-3 px-4 text-left font-semibold text-gray-300 cursor-pointer"
                                ng-click="sortBy('status')">
                                Status
                                <i ng-class="{'fas fa-sort-up': sortColumn === 'status' && !sortReverse,
                                'fas fa-sort-down': sortColumn === 'status' && sortReverse,
                                'fas fa-sort': sortColumn !== 'status'}" class="text-xs"></i>
                            </th>

                            <th class="py-3 px-4 text-left font-semibold text-gray-300">Last Login</th>
                            <!-- <th class="py-3 px-4 text-left font-semibold text-gray-300">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="user in paginatedUsers()"
                            class="border-b border-[#fff1] hover:bg-[#fff1] transition-colors">
                            <!-- <td class="py-3 px-4">
                            <input type="checkbox" class="rounded bg-[#0006] border-[#fff2]">
                        </td> -->
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center">
                                        <span class="font-semibold text-white">{{ user.name.charAt(0) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ user.name }}</p>
                                        <p class="text-sm text-gray-400">{{ user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="bg-[#0006] text-cyan-400 px-3 py-1 rounded-full text-sm">
                                    {{ user.group_name }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium" ng-class="{
                                      'bg-green-500/20 text-green-400': user.status === 'active',
                                      'bg-red-500/20 text-red-400': user.status === 'inactive',
                                      'bg-yellow-500/20 text-yellow-400': user.status === 'suspended'
                                  }">
                                    {{ user.status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-400">
                                {{ user.last_login || 'Never' }}
                            </td>
                            <!-- <td class="py-3 px-4">
                            <div class="flex items-center space-x-2"> -->
                            <!-- Edit -->
                            <!-- <button ng-click="editUser(user)"
                                    class="text-cyan-400 hover:text-cyan-300 transition-colors" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </button> -->

                            <!-- Permissions -->
                            <!-- <button ng-click="managePermissions(user)"
                                    class="text-purple-400 hover:text-purple-300 transition-colors"
                                    title="Manage Permissions">
                                    <i class="fas fa-key"></i>
                                </button> -->

                            <!-- Suspend/Activate -->
                            <!-- <button ng-if="user.status === 'Active'" ng-click="toggleUserStatus(user)"
                                    class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                    title="Suspend User">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button ng-if="user.status !== 'Active'" ng-click="toggleUserStatus(user)"
                                    class="text-green-400 hover:text-green-300 transition-colors" title="Activate User">
                                    <i class="fas fa-play"></i>
                                </button> -->

                            <!-- Delete -->
                            <!-- <button ng-click="deleteUser(user)"
                                    class="text-red-400 hover:text-red-300 transition-colors" title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap justify-center md:justify-between items-center p-4 border-t border-[#fff2]">
            <div class="text-gray-400 text-sm">
                Showing {{ (currentPage - 1) * (pageSize === 'All' ? totalUsers : pageSize) + 1 }} to {{ getRangeEnd()
                }} of {{ totalUsers }} users
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0">
                <button ng-click="previousPage()" ng-disabled="currentPage === 1 || pageSize === 'All'"
                    class="px-3 py-1 rounded border border-[#fff2] text-gray-400 hover:bg-[#fff2] transition-colors"
                    ng-class="{'opacity-50 cursor-not-allowed': currentPage === 1 || pageSize === 'All'}">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="flex space-x-1">
                    <button ng-repeat="page in getPages()" ng-click="goToPage(page)"
                        class="w-8 h-8 rounded border transition-colors"
                        ng-class="page === currentPage ? 'bg-cyan-600 border-cyan-600 text-white' : 'border-[#fff2] text-gray-400 hover:bg-[#fff2]'">
                        {{ page }}
                    </button>
                </div>

                <button ng-click="nextPage()" ng-disabled="currentPage === totalPages || pageSize === 'All'"
                    class="px-3 py-1 rounded border border-[#fff2] text-gray-400 hover:bg-[#fff2] transition-colors"
                    ng-class="{'opacity-50 cursor-not-allowed': currentPage === totalPages || pageSize === 'All'}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div ng-cloak ng-if="!loading && users.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-users text-gray-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No Users Found</h3>
            <p class="text-gray-400 mb-6">Get started by adding your first user to the system.</p>
            <a href="add_user"
                class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">
                Add First User
            </a>
        </div>
    </div>

    <!-- No Results State -->
    <div ng-cloak ng-if="!loading && users.length > 0 && filteredUsers.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-search text-gray-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No Users Match Your Search</h3>
            <p class="text-gray-400">Try adjusting your filters or search terms.</p>
        </div>
    </div>
</div>

<?php $this->end(); ?>