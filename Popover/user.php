<div class="api-info">
    <h4 class="font-semibold text-blue-900 mb-2">User Registration Form</h4>
    <p class="text-blue-800 text-sm">This form is dynamically generated from PHP backend</p>
</div>

<form id="userForm" class="space-y-4">
    <div class="form-group">
        <label class="form-label" for="fullName">Full Name</label>
        <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Enter your full name" required>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter your phone number">
    </div>
    
    <div class="form-group">
        <label class="form-label" for="department">Department</label>
        <select id="department" name="department" class="form-select" required>
            <option value="">Select Department</option>
            <option value="sales">Sales</option>
            <option value="marketing">Marketing</option>
            <option value="it">IT</option>
            <option value="hr">Human Resources</option>
            <option value="finance">Finance</option>
        </select>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="role">Role</label>
        <select id="role" name="role" class="form-select" required>
            <option value="">Select Role</option>
            <option value="manager">Manager</option>
            <option value="supervisor">Supervisor</option>
            <option value="employee">Employee</option>
            <option value="intern">Intern</option>
        </select>
    </div>
    
    <div class="form-group">
        <label class="form-label">
            <input type="checkbox" name="agreeTerms" required>
            <span class="ml-2">I agree to the terms and conditions</span>
        </label>
    </div>
    
    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" class="popover-button popover-button-secondary" data-action="cancel">
            Cancel
        </button>
        <button type="submit" class="popover-button popover-button-primary">
            <i class="fas fa-user-plus mr-1"></i>
            Register User
        </button>
    </div>
</form>