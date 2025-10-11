/**
 * Itinerary Activity Customization
 * Handles inline editing, adding, deleting, and reordering activities
 */

(function() {
    'use strict';

    // Store itinerary ID globally
    let itineraryId = null;

    /**
     * Initialize the customization features
     */
    function init() {
        // Get itinerary ID from data attribute
        const itineraryElement = document.querySelector('[data-itinerary-id]');
        if (itineraryElement) {
            itineraryId = itineraryElement.dataset.itineraryId;
        }

        // Attach event listeners
        attachEditButtonListeners();
        attachDeleteButtonListeners();
        attachAddActivityListeners();
        attachSaveButtonListeners();
        attachCancelButtonListeners();
        
        // Initialize drag and drop if SortableJS is available
        if (typeof Sortable !== 'undefined') {
            initializeDragAndDrop();
        }
    }

    /**
     * Attach listeners to all edit buttons
     */
    function attachEditButtonListeners() {
        document.querySelectorAll('.edit-activity-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const activityRow = this.closest('.activity-row');
                enableEditMode(activityRow);
            });
        });
    }

    /**
     * Enable edit mode for an activity row
     */
    function enableEditMode(activityRow) {
        // Get current values
        const timeElement = activityRow.querySelector('.activity-time');
        const durationElement = activityRow.querySelector('.activity-duration');
        const activityElement = activityRow.querySelector('.activity-name');
        const descriptionElement = activityRow.querySelector('.activity-description');

        const currentTime = timeElement.textContent.trim();
        const currentDuration = durationElement.textContent.match(/\d+/)[0]; // Extract number
        const currentActivity = activityElement.textContent.trim();
        const currentDescription = descriptionElement ? descriptionElement.textContent.trim() : '';

        // Replace with input fields
        timeElement.innerHTML = `<input type="time" class="form-control form-control-sm edit-time" value="${currentTime}" />`;
        durationElement.innerHTML = `<input type="number" class="form-control form-control-sm edit-duration" value="${currentDuration}" min="1" /> min`;
        activityElement.innerHTML = `<input type="text" class="form-control form-control-sm edit-activity" value="${currentActivity}" />`;
        
        if (descriptionElement) {
            descriptionElement.innerHTML = `<textarea class="form-control form-control-sm edit-description" rows="2">${currentDescription}</textarea>`;
        }

        // Show save/cancel buttons, hide edit/delete buttons
        activityRow.querySelector('.edit-activity-btn').style.display = 'none';
        activityRow.querySelector('.delete-activity-btn').style.display = 'none';
        activityRow.querySelector('.save-activity-btn').style.display = 'inline-block';
        activityRow.querySelector('.cancel-edit-btn').style.display = 'inline-block';
    }

    /**
     * Attach listeners to save buttons
     */
    function attachSaveButtonListeners() {
        document.querySelectorAll('.save-activity-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const activityRow = this.closest('.activity-row');
                saveActivityChanges(activityRow);
            });
        });
    }

    /**
     * Save activity changes via AJAX
     */
    function saveActivityChanges(activityRow) {
        const activityIndex = activityRow.dataset.activityIndex;
        
        // Get edited values
        const time = activityRow.querySelector('.edit-time').value;
        const duration = activityRow.querySelector('.edit-duration').value;
        const activity = activityRow.querySelector('.edit-activity').value;
        const description = activityRow.querySelector('.edit-description')?.value || '';

        // Send AJAX request
        fetch(`/itinerary/${itineraryId}/activity/${activityIndex}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                time: time,
                duration: duration,
                activity: activity,
                description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display with new values
                const timeElement = activityRow.querySelector('.activity-time');
                const durationElement = activityRow.querySelector('.activity-duration');
                const activityElement = activityRow.querySelector('.activity-name');
                const descriptionElement = activityRow.querySelector('.activity-description');

                timeElement.textContent = time;
                durationElement.innerHTML = `<i class="fas fa-clock text-muted"></i> ${duration} min`;
                activityElement.textContent = activity;
                
                if (descriptionElement) {
                    descriptionElement.textContent = description;
                }

                // Reset buttons
                activityRow.querySelector('.save-activity-btn').style.display = 'none';
                activityRow.querySelector('.cancel-edit-btn').style.display = 'none';
                activityRow.querySelector('.edit-activity-btn').style.display = 'inline-block';
                activityRow.querySelector('.delete-activity-btn').style.display = 'inline-block';

                // Show success message
                showToast('Activity updated successfully', 'success');
            } else {
                showToast('Failed to update activity', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving activity:', error);
            showToast('Error updating activity', 'error');
        });
    }

    /**
     * Attach listeners to cancel buttons
     */
    function attachCancelButtonListeners() {
        document.querySelectorAll('.cancel-edit-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const activityRow = this.closest('.activity-row');
                cancelEdit(activityRow);
            });
        });
    }

    /**
     * Cancel editing and restore original values
     */
    function cancelEdit(activityRow) {
        // Simply reload the page to reset - or implement proper state restoration
        location.reload();
    }

    /**
     * Attach listeners to delete buttons
     */
    function attachDeleteButtonListeners() {
        document.querySelectorAll('.delete-activity-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this activity?')) {
                    const activityRow = this.closest('.activity-row');
                    deleteActivity(activityRow);
                }
            });
        });
    }

    /**
     * Delete an activity via AJAX
     */
    function deleteActivity(activityRow) {
        const activityIndex = activityRow.dataset.activityIndex;

        fetch(`/itinerary/${itineraryId}/activity/${activityIndex}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row with animation
                activityRow.style.transition = 'opacity 0.3s';
                activityRow.style.opacity = '0';
                setTimeout(() => {
                    activityRow.remove();
                }, 300);

                showToast('Activity deleted successfully', 'success');
            } else {
                showToast('Failed to delete activity', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting activity:', error);
            showToast('Error deleting activity', 'error');
        });
    }

    /**
     * Attach listeners to "Add Activity" buttons
     */
    function attachAddActivityListeners() {
        document.querySelectorAll('.add-activity-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const day = this.dataset.day;
                showAddActivityModal(day);
            });
        });
    }

    /**
     * Show modal for adding a new activity
     */
    function showAddActivityModal(day) {
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="addActivityModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add Custom Activity - Day ${day}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addActivityForm">
                                <input type="hidden" name="day" value="${day}" />
                                
                                <div class="mb-3">
                                    <label for="activityTime" class="form-label">Time</label>
                                    <input type="time" class="form-control" id="activityTime" name="time" required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="activityDuration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="activityDuration" name="duration" min="1" value="30" required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="activityName" class="form-label">Activity Name</label>
                                    <input type="text" class="form-control" id="activityName" name="activity" maxlength="255" required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="activityDescription" class="form-label">Description (Optional)</label>
                                    <textarea class="form-control" id="activityDescription" name="description" rows="3" maxlength="500"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="saveNewActivity">Add Activity</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        const existingModal = document.getElementById('addActivityModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addActivityModal'));
        modal.show();

        // Attach save handler
        document.getElementById('saveNewActivity').addEventListener('click', function() {
            saveNewActivity(modal);
        });
    }

    /**
     * Save a new activity via AJAX
     */
    function saveNewActivity(modal) {
        const form = document.getElementById('addActivityForm');
        const formData = new FormData(form);

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        fetch(`/itinerary/${itineraryId}/activity`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                modal.hide();
                showToast('Activity added successfully', 'success');
                
                // Reload page to show new activity
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('Failed to add activity', 'error');
            }
        })
        .catch(error => {
            console.error('Error adding activity:', error);
            showToast('Error adding activity', 'error');
        });
    }

    /**
     * Initialize drag and drop for reordering activities
     */
    function initializeDragAndDrop() {
        document.querySelectorAll('.activity-list').forEach(list => {
            new Sortable(list, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Get new order of activity IDs
                    const activityIds = Array.from(list.querySelectorAll('.activity-row'))
                        .map(row => row.dataset.activityId)
                        .filter(id => id); // Filter out undefined

                    if (activityIds.length > 0) {
                        saveActivityOrder(activityIds);
                    }
                }
            });
        });
    }

    /**
     * Save new activity order via AJAX
     */
    function saveActivityOrder(order) {
        fetch(`/itinerary/${itineraryId}/activity/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Activities reordered successfully', 'success');
            } else {
                showToast('Failed to reorder activities', 'error');
            }
        })
        .catch(error => {
            console.error('Error reordering activities:', error);
            showToast('Error reordering activities', 'error');
        });
    }

    /**
     * Show toast notification
     */
    function showToast(message, type = 'info') {
        // Use existing toast system if available, or create a simple alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type === 'error' ? 'error' : 'success',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
