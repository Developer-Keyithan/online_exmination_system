<div id="remove_section_modal">
    <div class="relative">
        <h3 class="text-lg font-medium text-gray-100 mb-4">Remove Question from Section</h3>
        <div class="space-y-4">
            <form id="remove_question_to_section_form" onsubmit="return false" class="form-group">
                <label class="form-label">Select Section</label>
                <select ng-model="unassignSectionId" class="form-input select2" style="width: 100%;"
                    name="remove_section_id">
                    <option value="">-- Select a Section --</option>
                    <option ng-repeat="section in selectedQuestionAssignedSections" value="{{section.id}}">
                        Section: {{section.title}}
                    </option>
                </select>
            </form>

            <div class="bg-yellow-600 bg-opacity-20 border border-yellow-600 rounded-lg p-3">
                <p class="text-yellow-400 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This question will be removed from the selected section.
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" ng-click="closeUnassingModal()"
                    class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" ng-click="confirmUnassignSection()" ng-disabled="!unassignSectionId"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50">
                    Remove From this section
                </button>
            </div>
        </div>
    </div>
</div>