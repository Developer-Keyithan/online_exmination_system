<div id="create-user-group-modal" class="">
    <div class="rounded-xl shadow-2xl w-full">

        <!-- Form -->
        <div class="">
            <form id="user-group-create-and-edit-form" onsubmit="return false">
                <div class="space-y-4">

                    <!-- Group Name -->
                    <div>
                        <label for="group_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Group Name
                        </label>

                        <input type="text" id="group_name" name="group_name" ng-model="group.name" class="w-full backdrop-blur bg-[#fff2]"
                            placeholder="Enter group name" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="group_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>

                        <textarea id="group_description" name="group_description" ng-model="group.description"
                            rows="3" class="w-full backdrop-blur bg-[#fff2]" placeholder="Enter group description"></textarea>
                    </div>

                </div>

                <!-- Actions -->
                <!-- <div class="flex space-x-3 mt-6">
                    <button type="button" ng-click="closeModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>

                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                        {{isEditing ? 'Update' : 'Create'}} Group
                    </button>
                </div> -->
            </form>
        </div>
    </div>
</div>