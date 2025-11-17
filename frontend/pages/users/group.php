<?php $this->extend('frontend'); ?>
<?php $this->controller('UserController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">User Groups Management</h1>
        <p class="text-gray-400">Manage user groups and their permissions</p>
    </div>

    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading User Groups...</h3>
            <p class="text-gray-400">Please wait while we fetch your data.</p>
        </div>
    </div>

    <!-- Create Group Button -->
    <div class="mb-6" ng-cloak ng-if="!loading">
        <button ng-click="createGroup()"
            class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Create New Group</span>
        </button>
    </div>

    <!-- User Groups Grid -->
    <div ng-cloak ng-if="!loading && userGroups.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div ng-repeat="group in userGroups"
            class="bg-[#0003] rounded-xl shadow-md border border-[#fff2] hover:shadow-lg transition-shadow">
            <!-- Group Header -->
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-200">{{group.name}}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{group.members_count || 0}} member(s)</p>
                    </div>
                    <div class="relative">
                        <button ng-click="toggleGroupMenu(group.id)"
                            class="text-gray-400 p-1 rounded-lg hover:bg-[#fff3] transition-colors">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div ng-if="activeGroupMenu === group.id"
                            class="absolute right-0 top-8 bg-[#0003] backdrop-blur rounded-lg shadow-lg border border-[#fff2] p-2 z-10 min-w-[200px]">
                            <button ng-click="editGroup(group)"
                                class="w-full text-left px-4 py-2 text-sm text-cyan-500 hover:bg-[#12aac815] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-edit text-cyan-500"></i>
                                <span>Edit Group</span>
                            </button>
                            <button ng-click="setPermissions(group)"
                                class="w-full text-left px-4 py-2 text-sm text-green-500 hover:bg-[#0f01] transition-colors duration-300 hover:text-green-200 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-shield-alt text-green-500"></i>
                                <span>Set Permissions</span>
                            </button>
                            <button
                                ng-if="theLoggedUser.role === '1' || theLoggedUser.role === '2' || theLoggedUser.role === 1 || theLoggedUser.role === 2"
                                class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-[#f001] transition-colors duration-300 hover:text-red-200 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-trash"></i>
                                <span>Delete Group</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Group Description -->
            <div class="p-4">
                <p class="text-gray-400 text-sm">{{group.description || 'No description provided'}}</p>
            </div>

            <!-- Permissions Preview -->
            <div class="px-4 pb-3" ng-if="group.permission && group.permission.length > 0">
                <div class="flex flex-wrap gap-1">
                    <!-- <span ng-repeat="permission in group.permission.slice(0, 3)"
                        class=" text-cyan-200 bg-[#12aac830] text-sm px-3 py-1 rounded-full">
                        {{permission}}
                    </span> -->
                    <span ng-if="group.permission.length > 3"
                        class="bg-[#12aac830] text-cyan-200 text-xs px-2 py-1 rounded-full">
                        {{group.permission.length}} Permissions
                    </span>
                </div>
            </div>

            <!-- No Permissions Message -->
            <div class="px-4 pb-3" ng-if="!group.permission || group.permission.length === 0">
                <span class="bg-[#ff03] text-yellow-200 text-sm px-3 py-1 rounded-full">
                    No permissions set
                </span>
            </div>

            <!-- Group Actions -->
            <div class="p-4 flex items-end space-x-2">
                <button ng-click="viewMembers(group)"
                    class="flex-1 bg-[#12aac820] hover:bg-[#12aac850] text-gray-100 py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1">
                    <i class="fas fa-users"></i>
                    <span>Members</span>
                </button>
                <button ng-click="setPermissions(group)"
                    class="flex-1 bg-[#0f02] hover:bg-[#0f03] text-gray-100 py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1">
                    <i class="fas fa-shield-alt"></i>
                    <span>Permissions</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div ng-cloak ng-if="!loading && userGroups.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No user groups yet</h3>
            <p class="text-gray-400 mb-6">Create your first user group to get started with permission management.</p>
            <button ng-click="createGroup()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Create User Group</span>
            </button>
        </div>
    </div>

    <!-- Error State -->
    <div ng-cloak ng-if="error" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Failed to load user groups</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <button ng-click="loadUserGroups()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-redo"></i>
                <span>Try Again</span>
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal Template -->
<script type="text/ng-template" id="deleteConfirmationModal">
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-6 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Delete User Group</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete the group "{{groupToDelete.name}}"? This action cannot be undone.
                </p>
                <div class="flex space-x-3">
                    <button 
                        ng-click="closeDeleteModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        ng-click="confirmDelete()"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors"
                    >
                        Delete Group
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>
<?php $this->end(); ?>