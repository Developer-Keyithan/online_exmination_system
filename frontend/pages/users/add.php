<?php
$this->extend('frontend');
$this->controller('UserController');

$user = user_id(); // Logged-in user

$sql = "SELECT id, name FROM user_group";

// Role-based filters
if ($user == 1) {
    $sql .= " WHERE id != 1";
} else if ($user == 2) {
    $sql .= " WHERE id NOT IN (1,2)";
} else if ($user == 3) {
    $sql .= " WHERE id NOT IN (1,2,3)";
} else {
    $sql .= " WHERE id NOT IN (1,2,3,4)";
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
            <h1 class="text-2xl font-bold">Add New User</h1>
            <p class="text-gray-400">Create a new user account in the system</p>
        </div>
        <a href="users"
            class="bg-gray-600 hover:bg-gray-700 mt-4 md:mt-0 w-fit text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Users</span>
        </a>
    </div>

    <!-- Add User Form -->
    <div ng-cloak class="max-w-4xl mx-auto">
        <form id="add-user-form" name="addUserForm" novalidate onsubmit="return false">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information Section -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-100 mb-4 pb-2 border-b border-gray-600">Personal
                        Information</h2>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullName" class="form-label">Full Name <span class="text-red-700">*</span></label>
                    <input type="text" id="fullName" ng-model="userData.name" class="form-input" name="fullname"
                        placeholder="Enter full name">
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.fullName.$error.required">
                        Full name is required
                    </div>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address <span class="text-red-700">*</span></label>
                    <input type="email" id="email" ng-model="userData.email" class="form-input" name="email"
                        placeholder="Enter email address">
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.email.$error.required">
                        Email is required
                    </div>
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.email.$error.email">
                        Please enter a valid email address
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number <span class="text-red-700">*</span></label>
                    <input type="tel" id="phone" ng-model="userData.phone" class="form-input" maxlength="10" name="phone"
                        placeholder="Enter phone number">
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.phone.$error.required">
                        Phone Number is required
                    </div>
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.phone.$error.phone">
                        Please enter a valid phone number (e.g., 7 12345678)
                    </div>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username <span class="text-red-700">*</span></label>
                    <input type="text" id="username" ng-model="userData.username" class="form-input" name="username"
                        placeholder="Enter username">
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.username.$error.required">
                        Username is required
                    </div>
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.username.$error.username">
                        Please enter a valid username (alphanumeric, 4-20 characters)
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="md:col-span-2 mt-4">
                    <h2 class="text-lg font-semibold text-gray-100 mb-4 pb-2 border-b border-gray-600">Account
                        Information</h2>
                </div>

                <!-- User Group -->
                <div class="form-group relative">
                    <label class="form-label">User Group <span class="text-red-700">*</span></label>

                    <!-- Selected Box -->
                    <div class="w-full bg-[#0004] border border-gray-600 rounded px-3 py-2 text-white cursor-pointer flex justify-between items-center"
                        ng-click="dropdownOpen = !dropdownOpen">
                        <span>{{ userData.user_group || 'Select User Group' }}</span>
                        <span class="ml-2">&#9662;</span>
                    </div>

                    <!-- Dropdown List -->
                    <div class="absolute w-full bg-[#0004] p-2 backdrop-blur border border-gray-600 rounded mt-1 z-10 max-h-60 overflow-y-auto"
                        ng-show="dropdownOpen">
                        <?php foreach ($userGroups as $group): ?>
                            <div class="px-3 py-2 hover:bg-cyan-700/50 text-white cursor-pointer rounded"
                                ng-click="userData.user_group = '<?= $group['name'] ?>'; dropdownOpen = false; "
                                ng-class="userData.user_group === '<?= $group['name'] ?>' ? 'bg-cyan-900/50' : ''">
                                <?= $group['name'] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Hidden Input (REAL value submitted here) -->
                    <input type="hidden" name="userGroup" ng-value="userData.user_group">

                    <div class="error-message text-red-500 text-sm"
                        ng-show="addUserForm.submitted && !userData.user_group">
                        User group is required
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group relative">
                    <label class="form-label">Status <span class="text-red-700">*</span></label>

                    <!-- Selected Box -->
                    <div class="w-full bg-[#0004] border border-gray-600 rounded px-3 py-2 text-white cursor-pointer flex justify-between items-center"
                        ng-click="statusDropdownOpen = !statusDropdownOpen">
                        <span>{{ userData.status || 'Select Status' }}</span>
                        <span class="ml-2">&#9662;</span>
                    </div>

                    <!-- Dropdown List -->
                    <div class="absolute w-full bg-[#0004] backdrop-blur p-2 border border-gray-600 rounded mt-1 z-10 max-h-60 overflow-y-auto"
                        ng-show="statusDropdownOpen">
                        <div class="px-3 py-2 hover:bg-cyan-700/50 text-white cursor-pointer rounded"
                            ng-click="userData.status = 'active'; statusDropdownOpen = false;"
                            ng-class="userData.status === 'active' ? 'bg-cyan-900/50' : ''">
                            Active
                        </div>
                        <div class="px-3 py-2 hover:bg-cyan-700/50 text-white cursor-pointer rounded"
                            ng-click="userData.status = 'inactive'; statusDropdownOpen = false;"
                            ng-class="userData.status === 'inactive' ? 'bg-cyan-900/50' : ''">
                            Inactive
                        </div>
                        <div class="px-3 py-2 hover:bg-cyan-700/50 text-white cursor-pointer rounded"
                            ng-click="userData.status = 'suspended'; statusDropdownOpen = false;"
                            ng-class="userData.status === 'suspended' ? 'bg-cyan-900/50' : ''">
                            Suspended
                        </div>

                    </div>

                    <!-- Hidden Input for form submission -->
                    <input type="hidden" name="status" ng-value="userData.status" required>

                    <div class="error-message text-red-500 text-sm"
                        ng-show="addUserForm.submitted && addUserForm.status.$error.required">
                        Status is required
                    </div>
                </div>

                <!-- Password Section -->
                <div class="md:col-span-2 mt-4">
                    <h2 class="text-lg font-semibold text-gray-100 mb-4 pb-2 border-b border-gray-600">Password</h2>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password <span class="text-red-700">*</span></label>
                    <div class="relative">
                        <input type="password" id="password" ng-model="userData.password" class="form-input pr-10"
                            name="password" placeholder="Enter password" ng-minlength="3" minlength="3">
                        <button type="button"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-300"
                            ng-click="togglePasswordVisibility('password')">
                            <i class="fas" ng-class="passwordVisible.password ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.password.$error.required">
                        Password is required
                    </div>
                    <div class="error-message" ng-show="addUserForm.submitted && addUserForm.password.$error.minlength">
                        Password must be at least 6 characters long
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password <span
                            class="text-red-700">*</span></label>
                    <div class="relative">
                        <input type="password" id="confirmPassword" ng-model="userData.confirmPassword" name="cpassword"
                            class="form-input pr-10" placeholder="Confirm password" match-password="userData.password"
                            minlength="3">
                        <button type="button"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-300"
                            ng-click="togglePasswordVisibility('confirmPassword')">
                            <i class="fas" ng-class="passwordVisible.confirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <div class="error-message"
                        ng-show="addUserForm.submitted && addUserForm.confirmPassword.$error.required">
                        Please confirm your password
                    </div>
                    <div class="error-message"
                        ng-show="addUserForm.submitted && addUserForm.confirmPassword.$error.passwordMatch">
                        Passwords do not match
                    </div>
                </div>

                <!-- Additional Information Section -->
                <!-- <div class="md:col-span-2 mt-4">
                    <h2 class="text-lg font-semibold text-gray-100 mb-4 pb-2 border-b border-gray-600">Additional
                        Information</h2>
                </div> -->

                <!-- Department -->
                <!-- <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" id="department" ng-model="userData.department" class="form-input"
                        name="department" placeholder="Enter department">
                </div> -->

                <!-- Position -->
                <!-- <div class="form-group">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" id="position" ng-model="userData.position" class="form-input" name="position"
                        placeholder="Enter position">
                </div> -->

                <!-- Notes -->
                <div class="form-group md:col-span-2">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" ng-model="userData.notes" rows="4" class="form-input" name="notes"
                        placeholder="Add any additional notes about this user..."></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-600">
                <button type="reset"
                    class="bg-red-500 hover:bg-red-700 text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
                <button type="buttonm" ng-disabled="loading" ng-click="submitUser()"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-user-plus" ng-class="{'fa-spin animate-spin': loading}"></i>
                    <span>{{ loading ? 'Creating User...' : 'Create User' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php $this->end(); ?>