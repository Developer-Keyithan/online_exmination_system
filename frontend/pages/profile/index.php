<?php $this->extend('frontend'); ?>
<?php $this->controller('ProfileController'); ?>

<?php $this->start('content'); ?>
<div ng-controller="ProfileController" ng-init="init()" class="min-h-screen mb-16">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-100">Profile Settings</h1>
        <p class="text-gray-400">Manage your account information and security</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Profile Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Profile Information Card -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-100">Profile Information</h2>
                    <!-- <button ng-click="toggleEdit()" ng-class="{'bg-cyan-600': !editing, 'bg-yellow-600': editing}"
                        class="px-4 py-2 rounded-lg text-white hover:opacity-90 transition-opacity">
                        {{editing ? 'Cancel' : 'Edit Profile'}}
                    </button> -->
                </div>

                <div class="space-y-6">
                    <!-- Profile Picture -->
                    <div class="flex items-center space-x-6">
                        <div class="relative">
                            <div
                                class="w-24 h-24 rounded-full bg-gradient-to-r from-cyan-500 to-purple-500 flex items-center justify-center text-white text-4xl font-bold">
                                {{user.name.charAt(0)}}
                            </div>
                            <input type="file" id="avatarUpload" class="hidden" accept="image/*" ng-disabled="!editing"
                                onchange="angular.element(this).scope().uploadAvatar(event)">
                            <label for="avatarUpload" ng-if="editing"
                                class="absolute bottom-0 right-0 bg-cyan-600 hover:bg-cyan-700 text-white p-2 rounded-full cursor-pointer">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-100">{{user.name}}</h3>
                            <p class="text-gray-400">{{user.email}}</p>
                            <p class="text-sm text-gray-400">{{getRoleName(user.role)}}</p>
                            <p ng-if="user.reg_no" class="text-sm text-cyan-400">ID: {{user.reg_no}}</p>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                            <input type="text" ng-model="profileData.name" ng-disabled="!editing" required
                                class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                                ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing, 'border-red-500': errors.name}">
                            <p ng-if="errors.name" class="text-red-400 text-sm mt-1">{{errors.name}}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Email Address *</label>
                            <input type="email" ng-model="profileData.email" ng-disabled="!editing" required
                                class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                                ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing, 'border-red-500': errors.email}">
                            <p ng-if="errors.email" class="text-red-400 text-sm mt-1">{{errors.email}}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Phone Number *</label>
                            <input type="tel" ng-model="user.phone" ng-change="formatSLPhone()" ng-disabled="!editing"
                                required class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                                ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing, 'border-red-500': errors.phone}">
                            <p ng-if="errors.phone" class="text-red-400 text-sm mt-1">{{errors.phone}}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Registration Number</label>
                            <input type="text" ng-model="profileData.reg_no" ng-disabled="!editing"
                                class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                                ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing}">
                        </div>

                        <div ng-if="user.role === 6" class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Username *</label>
                            <input type="text" ng-model="profileData.username" ng-disabled="!editing" required
                                class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                                ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing, 'border-red-500': errors.username}">
                            <p ng-if="errors.username" class="text-red-400 text-sm mt-1">{{errors.username}}</p>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                        <textarea ng-model="profileData.note" ng-disabled="!editing" rows="3"
                            class="w-full px-4 py-3 bg-[#0007] border rounded-lg text-gray-100"
                            ng-class="{'border-[#fff2]': !editing, 'border-cyan-500': editing}"
                            placeholder="Additional information or notes..."></textarea>
                    </div>

                    <!-- Save Button -->
                    <!-- <div ng-if="editing" class="flex justify-end">
                        <button ng-click="saveProfile()" ng-disabled="saving"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center space-x-2 transition-colors"
                            ng-class="{'opacity-50 cursor-not-allowed': saving}">
                            <i class="fas" ng-class="saving ? 'fa-spinner animate-spin' : 'fa-save'"></i>
                            <span>{{saving ? 'Saving...' : 'Save Changes'}}</span>
                        </button>
                    </div> -->
                </div>
            </div>

            <!-- Security Card -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Security Settings</h2>

                <!-- Change Password Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Change Password</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Current Password *</label>
                            <div class="relative">
                                <input ng-attr-type="{{ showCurrentPassword ? 'text' : 'password' }}"
                                    ng-model="passwordData.current_password"
                                    class="input-with-eye w-full pl-4 py-3 pr-12 bg-[#0007] border border-[#fff2] rounded-lg text-gray-100"
                                    placeholder="Enter your account's current password">
                                <i class="fas absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-200"
                                    ng-class="showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'"
                                    ng-click="showCurrentPassword = !showCurrentPassword">
                                </i>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">New Password *</label>
                                <div class="relative">
                                    <input ng-attr-type="{{ showNewPassword ? 'text' : 'password' }}"
                                        ng-model="passwordData.new_password"
                                        class="input-with-eye w-full px-4 py-3 bg-[#0007] border border-[#fff2] rounded-lg text-gray-100"
                                        placeholder="Enter your new password">
                                    <i class="fas absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-200"
                                        ng-class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"
                                        ng-click="showNewPassword = !showNewPassword">
                                    </i>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password *</label>
                                <div class="relative">
                                    <input ng-attr-type="{{ showConfirmPassword ? 'text' : 'password' }}"
                                        ng-model="passwordData.confirm_password"
                                        class="input-with-eye w-full px-4 py-3 bg-[#0007] border border-[#fff2] rounded-lg text-gray-100"
                                        ng-class="{'bg-red-800/30': passwordData.confirm_password && passwordData.confirm_password !== passwordData.new_password}"
                                        placeholder="Re-enter your new password">
                                    <i class="fas absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-200"
                                        ng-class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"
                                        ng-click="showConfirmPassword = !showConfirmPassword">
                                    </i>
                                </div>
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="bg-[#0007] p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-300 mb-2">Password Requirements:</h4>
                            <ul class="space-y-1 text-sm">
                                <li class="flex items-center"
                                    ng-class="passwordRequirements.length ? 'text-green-400' : 'text-gray-400'">
                                    <i class="fas fa-check-circle mr-2" ng-if="passwordRequirements.length"></i>
                                    <i class="fas fa-times-circle mr-2" ng-if="!passwordRequirements.length"></i>
                                    At least 8 characters long
                                </li>
                                <li class="flex items-center"
                                    ng-class="passwordRequirements.uppercase ? 'text-green-400' : 'text-gray-400'">
                                    <i class="fas fa-check-circle mr-2" ng-if="passwordRequirements.uppercase"></i>
                                    <i class="fas fa-times-circle mr-2" ng-if="!passwordRequirements.uppercase"></i>
                                    At least one uppercase letter
                                </li>
                                <li class="flex items-center"
                                    ng-class="passwordRequirements.lowercase ? 'text-green-400' : 'text-gray-400'">
                                    <i class="fas fa-check-circle mr-2" ng-if="passwordRequirements.lowercase"></i>
                                    <i class="fas fa-times-circle mr-2" ng-if="!passwordRequirements.lowercase"></i>
                                    At least one lowercase letter
                                </li>
                                <li class="flex items-center"
                                    ng-class="passwordRequirements.number ? 'text-green-400' : 'text-gray-400'">
                                    <i class="fas fa-check-circle mr-2" ng-if="passwordRequirements.number"></i>
                                    <i class="fas fa-times-circle mr-2" ng-if="!passwordRequirements.number"></i>
                                    At least one number
                                </li>
                            </ul>
                        </div>

                        <!-- Password Strength -->
                        <div ng-if="passwordData.new_password">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-300">Password Strength:</span>
                                <span class="text-sm font-medium" ng-class="{
                                          'text-red-400': passwordStrength < 2,
                                          'text-yellow-400': passwordStrength >= 2 && passwordStrength < 3,
                                          'text-green-400': passwordStrength >= 3
                                      }">
                                    {{passwordStrength < 2 ? 'Weak' : passwordStrength < 3 ? 'Medium' : 'Strong' }}
                                        </span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300" ng-class="{
                                         'bg-red-500': passwordStrength < 2,
                                         'bg-yellow-500': passwordStrength >= 2 && passwordStrength < 3,
                                         'bg-green-500': passwordStrength >= 3
                                     }" ng-style="{'width': (passwordStrength * 25) + '%'}"></div>
                            </div>
                        </div>

                        <!-- Change Password Button -->
                        <div class="flex justify-end">
                            <button ng-click="changePassword()" ng-disabled="changingPassword || !isPasswordFormValid()"
                                class="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg flex items-center space-x-2 transition-colors"
                                ng-class="{'opacity-50 cursor-not-allowed': changingPassword || !isPasswordFormValid()}">
                                <i class="fas" ng-class="changingPassword ? 'fa-spinner animate-spin' : 'fa-key'"></i>
                                <span>{{changingPassword ? 'Changing...' : 'Change Password'}}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Account Status & Sessions -->
        <div class="space-y-8">
            <!-- Account Status -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Account Status</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Account Type</span>
                        <span class="font-medium text-cyan-400">{{getRoleName(user.role)}}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Member Since</span>
                        <span class="text-gray-300">{{user.created_at | date:'MMM d, yyyy'}}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Last Updated</span>
                        <span class="text-gray-300">{{user.updated_at | date:'MMM d, yyyy'}}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Account Status</span>
                        <span
                            ng-class="{'text-green-400': user.status === 0,'text-orange-400': user.status === 1,'text-red-400': user.status === 2,'text-red-600': user.status === 3}">
                            {{user.status === 0 ? 'Active' : user.status === 1 ? 'Inactive' : user.status === 2 ?
                            'Suspended' : user.status === 3 ? 'Deleted' : 'Unknown'}}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-300">Last Login</span>
                        <span class="text-gray-300" live-from-now="{{lastLogin}}"></span>

                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-[#fff2]">
                    <button ng-click="exportData()"
                        class="w-full flex items-center justify-center space-x-2 p-3 rounded-lg bg-[#0007] hover:bg-[#0009] text-gray-300 transition-colors mb-3">
                        <i class="fas fa-download"></i>
                        <span>Export My Data</span>
                    </button>
                </div>
            </div>

            <!-- Danger Zone -->
            <div ng-if="user.role !== 1 && user.role !== 2" class="bg-[#0005] p-6 rounded-xl border border-red-500/30">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Danger Zone</h2>

                <div class="space-y-4">
                    <button ng-click="deleteAccount()"
                        class="w-full flex items-center justify-center space-x-2 p-3 rounded-lg bg-red-600/20 hover:bg-red-600/30 text-red-300 border border-red-500/30 transition-colors">
                        <i class="fas fa-trash-alt"></i>
                        <span>Delete My Account</span>
                    </button>

                    <p class="text-sm text-gray-400 text-center">
                        This action cannot be undone. Your account will be permanently deleted.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>