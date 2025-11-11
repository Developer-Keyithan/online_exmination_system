
<div class="api-info">
    <h4 class="font-semibold text-rose-900 mb-2">Contact Us Form</h4>
    <p class="text-rose-800 text-sm">Get in touch with our team. We\'ll respond within 24 hours.</p>
</div>

<form id="contactForm" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-group">
            <label class="form-label" for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="form-input" placeholder="Enter first name" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="form-input" placeholder="Enter last name" required>
        </div>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="contactEmail">Email Address</label>
        <input type="email" id="contactEmail" name="contactEmail" class="form-input" placeholder="Enter your email" required>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="subject">Subject</label>
        <input type="text" id="subject" name="subject" class="form-input" placeholder="Enter subject" required>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="message">Message</label>
        <textarea id="message" name="message" rows="4" class="form-input" placeholder="Enter your message" required></textarea>
    </div>
    
    <div class="form-group">
        <label class="form-label" for="priority">Priority</label>
        <select id="priority" name="priority" class="form-select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
        </select>
    </div>
    
    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" class="popover-button popover-button-secondary" data-action="cancel">
            Cancel
        </button>
        <button type="submit" class="popover-button popover-button-primary">
            <i class="fas fa-paper-plane mr-1"></i>
            Send Message
        </button>
    </div>
</form>