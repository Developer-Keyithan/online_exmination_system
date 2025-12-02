<div id="assign_to_section_modal">
    <div class="relative">
        <h3 class="text-lg font-medium text-gray-100 mb-4">Assign Question to Section</h3>
        <div class="space-y-4">
            <form id="assign_question_to_section_form" onsubmit="return false" class="form-group">
                <label class="form-label">Select Section</label>
                <select ng-model="assignSectionId" class="form-input select2" style="width: 100%;"
                    name="new_section_id">
                    <option value="">-- Select a Section --</option>
                    <option ng-repeat="section in savedSections" value="{{section.id}}">
                        Section: {{section.title}}
                    </option>
                </select>
            </form>

            <div class="bg-yellow-600 bg-opacity-20 border border-yellow-600 rounded-lg p-3">
                <p class="text-yellow-400 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This question will be added to the selected section and count towards its question limit.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" ng-click="cancelAssignToSection()"
                    class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" ng-click="confirmAssignToSection()" ng-disabled="!assignSectionId"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50">
                    Assign to this section
                </button>
            </div>
        </div>
    </div>
</div>