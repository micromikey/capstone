<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <x-trail-breadcrumb />
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Community Posts') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Only show the Posts content section for organizations -->
                <div id="content-community-posts" class="tab-content active p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Community Posts</h2>
                        <button id="create-post-btn" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Post
                        </button>
                    </div>

                    <!-- Posts Grid -->
                    <div id="posts-container" class="space-y-6">
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg">Loading posts...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="create-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="create-post-form" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Promote Your Content</h3>
                            <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5">
                            <!-- Content Type Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    What would you like to promote? <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="content_type" value="trail" checked class="text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Trail</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="content_type" value="event" class="text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Event</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Trail Selection -->
                            <div id="trail-selection">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Trail <span class="text-red-500">*</span>
                                </label>
                                <select id="trail-select" name="trail_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Loading your trails...</option>
                                </select>
                            </div>

                            <!-- Event Selection -->
                            <div id="event-selection" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Event <span class="text-red-500">*</span>
                                </label>
                                <select id="event-select" name="event_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Loading your events...</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="content" id="post-content" rows="5" required maxlength="5000" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                    placeholder="Share what makes this trail or event special..."></textarea>
                                <p class="mt-1 text-xs text-gray-500"><span id="char-count">0</span>/5000 characters</p>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add Photos (Optional, max 10)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                                        </div>
                                        <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="hidden" max="10">
                                    </label>
                                </div>
                                
                                <!-- Image Previews -->
                                <div id="image-previews" class="grid grid-cols-3 gap-3 mt-4 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" id="cancel-post-btn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="submit-post-btn" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-medium hover:from-purple-700 hover:to-purple-800 transition-colors">
                            Publish Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="edit-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form id="edit-post-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-post-id" name="post_id">
                    
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="edit-modal-title">Edit Post</h3>
                            <button type="button" id="close-edit-modal-btn" class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5">
                            <!-- Content/Trail Info (Read-only) -->
                            <div id="edit-content-info" class="p-3 bg-gray-50 rounded-lg text-sm text-gray-700"></div>

                            <!-- Rating (for trail posts) -->
                            <div id="edit-rating-section" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex gap-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="edit_rating" value="1" class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2">⭐</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="edit_rating" value="2" class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2">⭐⭐</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="edit_rating" value="3" class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2">⭐⭐⭐</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="edit_rating" value="4" class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2">⭐⭐⭐⭐</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="edit_rating" value="5" class="text-yellow-500 focus:ring-yellow-500">
                                        <span class="ml-2">⭐⭐⭐⭐⭐</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="content" id="edit-post-content" rows="5" required maxlength="5000" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                    placeholder="Share what makes this trail or event special..."></textarea>
                                <p class="mt-1 text-xs text-gray-500"><span id="edit-char-count">0</span>/5000 characters</p>
                            </div>

                            <!-- Existing Images -->
                            <div id="edit-existing-images" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                                <div id="edit-existing-images-grid" class="grid grid-cols-3 gap-3"></div>
                            </div>

                            <!-- Add New Images -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add New Photos (Optional, max 10 total)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                                        </div>
                                        <input type="file" name="images[]" id="edit-images-input" multiple accept="image/*" class="hidden" max="10">
                                    </label>
                                </div>
                                
                                <!-- New Image Previews -->
                                <div id="edit-new-image-previews" class="grid grid-cols-3 gap-3 mt-4 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button" id="cancel-edit-btn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="submit-edit-btn" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-medium hover:from-purple-700 hover:to-purple-800 transition-colors">
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-post-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto" aria-labelledby="delete-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="delete-modal-title">
                                Delete Post
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this post? This action cannot be undone. All comments and likes will also be deleted.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" id="confirm-delete-btn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Delete Post
                    </button>
                    <button type="button" id="cancel-delete-btn" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="comments-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-2xl shadow-xl w-full max-w-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Comments</h3>
                        <button type="button" id="close-comments-btn" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="comments-list" class="space-y-4 max-h-96 overflow-y-auto mb-4">
                        <p class="text-gray-500 text-center py-4">Loading comments...</p>
                    </div>
                    
                    <form id="add-comment-form" class="flex gap-2">
                        <input type="text" id="comment-input" placeholder="Write a comment..." 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Post
                        </button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let userTrails = [];
        let userEvents = [];
        let currentPostId = null;
        let postToDelete = null;
        let postToEdit = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Organization Community Posts');
            
            // Load posts immediately on page load
            loadCommunityPosts();
            
            // Load organization's trails and events
            loadOrganizationContent();

        // Create post button handler
        const createPostBtn = document.getElementById('create-post-btn');
        if (createPostBtn) {
            createPostBtn.addEventListener('click', openCreatePostModal);
        }
        
        // Modal close handlers
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelPostBtn = document.getElementById('cancel-post-btn');
        const createPostModal = document.getElementById('create-post-modal');
        
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeCreatePostModal);
        if (cancelPostBtn) cancelPostBtn.addEventListener('click', closeCreatePostModal);
        
        // Click outside to close
        if (createPostModal) {
            createPostModal.addEventListener('click', function(e) {
                if (e.target === createPostModal) {
                    closeCreatePostModal();
                }
            });
        }
        
        // Content type toggle
        const contentTypeRadios = document.querySelectorAll('input[name="content_type"]');
        contentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleContentSelection(this.value);
            });
        });
        
        // Form submission
        const createPostForm = document.getElementById('create-post-form');
        if (createPostForm) {
            createPostForm.addEventListener('submit', handlePostSubmit);
        }
        
        // Character counter
        const postContent = document.getElementById('post-content');
        if (postContent) {
            postContent.addEventListener('input', function() {
                document.getElementById('char-count').textContent = this.value.length;
            });
        }
        
        // Image upload preview
        const imagesInput = document.getElementById('images-input');
        if (imagesInput) {
            imagesInput.addEventListener('change', handleImagePreview);
        }
        
        // Comments modal
        const closeCommentsBtn = document.getElementById('close-comments-btn');
        if (closeCommentsBtn) {
            closeCommentsBtn.addEventListener('click', closeCommentsModal);
        }
        
        const addCommentForm = document.getElementById('add-comment-form');
        if (addCommentForm) {
            addCommentForm.addEventListener('submit', handleAddComment);
        }
        
        // Edit modal handlers
        const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
        const cancelEditBtn = document.getElementById('cancel-edit-btn');
        const editPostModal = document.getElementById('edit-post-modal');
        const editPostForm = document.getElementById('edit-post-form');
        
        if (closeEditModalBtn) closeEditModalBtn.addEventListener('click', closeEditModal);
        if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);
        if (editPostForm) editPostForm.addEventListener('submit', handleEditSubmit);
        
        if (editPostModal) {
            editPostModal.addEventListener('click', function(e) {
                if (e.target === editPostModal) {
                    closeEditModal();
                }
            });
        }
        
        // Edit character counter
        const editPostContent = document.getElementById('edit-post-content');
        if (editPostContent) {
            editPostContent.addEventListener('input', function() {
                document.getElementById('edit-char-count').textContent = this.value.length;
            });
        }
        
        // Edit image upload preview
        const editImagesInput = document.getElementById('edit-images-input');
        if (editImagesInput) {
            editImagesInput.addEventListener('change', handleEditImagePreview);
        }
        
        // Delete modal handlers
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const deletePostModal = document.getElementById('delete-post-modal');
        
        if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
        if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', confirmDeletePost);
        
        if (deletePostModal) {
            deletePostModal.addEventListener('click', function(e) {
                if (e.target === deletePostModal) {
                    closeDeleteModal();
                }
            });
        }
        
        // Listen for open comments modal event
        window.addEventListener('openCommentsModal', function(e) {
            openCommentsModal(e.detail.postId);
        });
    });
    
    function loadOrganizationContent() {
        // Load trails
        fetch('/api/organization/trails', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            userTrails = data.trails || [];
            populateTrailSelect();
        })
        .catch(error => {
            console.error('Error loading trails:', error);
            populateTrailSelect();
        });
        
        // Load events
        fetch('/api/organization/events', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            userEvents = data.events || [];
            populateEventSelect();
        })
        .catch(error => {
            console.error('Error loading events:', error);
            populateEventSelect();
        });
    }
    
    function populateTrailSelect() {
        const select = document.getElementById('trail-select');
        if (!select) return;
        
        select.innerHTML = '<option value="">Select a trail to promote</option>';
        
        if (userTrails.length === 0) {
            select.innerHTML = '<option value="">No trails available</option>';
            return;
        }
        
        userTrails.forEach(trail => {
            const option = document.createElement('option');
            option.value = trail.id;
            option.textContent = trail.trail_name;
            select.appendChild(option);
        });
    }
    
    function populateEventSelect() {
        const select = document.getElementById('event-select');
        if (!select) return;
        
        select.innerHTML = '<option value="">Select an event to promote</option>';
        
        if (userEvents.length === 0) {
            select.innerHTML = '<option value="">No events available</option>';
            return;
        }
        
        userEvents.forEach(event => {
            const option = document.createElement('option');
            option.value = event.id;
            option.textContent = event.title;
            select.appendChild(option);
        });
    }
    
    function openCreatePostModal() {
        const modal = document.getElementById('create-post-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeCreatePostModal() {
        const modal = document.getElementById('create-post-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form
            const form = document.getElementById('create-post-form');
            if (form) form.reset();
            
            // Reset previews
            const previews = document.getElementById('image-previews');
            if (previews) {
                previews.innerHTML = '';
                previews.classList.add('hidden');
            }
            
            document.getElementById('char-count').textContent = '0';
        }
    }
    
    function toggleContentSelection(type) {
        const trailSection = document.getElementById('trail-selection');
        const eventSection = document.getElementById('event-selection');
        const trailSelect = document.getElementById('trail-select');
        const eventSelect = document.getElementById('event-select');
        
        if (type === 'trail') {
            trailSection.classList.remove('hidden');
            eventSection.classList.add('hidden');
            trailSelect.required = true;
            eventSelect.required = false;
            eventSelect.value = '';
        } else {
            trailSection.classList.add('hidden');
            eventSection.classList.remove('hidden');
            trailSelect.required = false;
            eventSelect.required = true;
            trailSelect.value = '';
        }
    }
    
    function handleImagePreview(e) {
        const files = Array.from(e.target.files);
        const previews = document.getElementById('image-previews');
        
        if (files.length === 0) {
            previews.classList.add('hidden');
            return;
        }
        
        if (files.length > 10) {
            alert('Maximum 10 images allowed');
            e.target.value = '';
            return;
        }
        
        previews.innerHTML = '';
        previews.classList.remove('hidden');
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                    <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600" data-index="${index}">×</button>
                `;
                previews.appendChild(div);
                
                div.querySelector('.remove-image').addEventListener('click', function() {
                    removeImage(index);
                });
            };
            reader.readAsDataURL(file);
        });
    }
    
    function removeImage(index) {
        const input = document.getElementById('images-input');
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        
        files.forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });
        
        input.files = dt.files;
        handleImagePreview({ target: input });
    }
    
    function handlePostSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-post-btn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Publishing...';
        
        const formData = new FormData(e.target);
        
        fetch('/community/posts', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', 'Your post has been published!', 'success');
                closeCreatePostModal();
                loadCommunityPosts();
            } else {
                showToast('Error', data.message || 'Failed to create post', 'error');
            }
        })
        .catch(error => {
            console.error('Error creating post:', error);
            showToast('Error', 'An error occurred while creating your post', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }
    
    function showToast(title, message, type = 'info') {
        // Simple toast notification
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };
        
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-[9999] transform transition-all duration-300`;
        toast.innerHTML = `
            <div class="font-bold">${title}</div>
            <div class="text-sm">${message}</div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
    
    function openCommentsModal(postId) {
        currentPostId = postId;
        const modal = document.getElementById('comments-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loadComments(postId);
        }
    }
    
    function closeCommentsModal() {
        const modal = document.getElementById('comments-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentPostId = null;
        }
    }
    
    function loadComments(postId) {
        const container = document.getElementById('comments-list');
        container.innerHTML = '<p class="text-gray-500 text-center py-4">Loading comments...</p>';
        
        fetch(`/community/posts/${postId}/comments`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.comments) {
                if (data.comments.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">No comments yet. Be the first to comment!</p>';
                } else {
                    container.innerHTML = '';
                    data.comments.forEach(comment => {
                        const div = document.createElement('div');
                        div.className = 'flex gap-3 p-3 hover:bg-gray-50 rounded-lg';
                        const avatarHtml = createAvatarHtml(comment.user, 'w-10 h-10', 'text-sm');
                        const userName = comment.user?.display_name || comment.user?.organization_name || comment.user?.name || 'Unknown';
                        div.innerHTML = `
                            ${avatarHtml}
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">${userName}</div>
                                <div class="text-sm text-gray-700">${escapeHtml(comment.comment)}</div>
                                <div class="text-xs text-gray-500 mt-1">${new Date(comment.created_at).toLocaleDateString()}</div>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            container.innerHTML = '<p class="text-red-500 text-center py-4">Failed to load comments</p>';
        });
    }
    
    function handleAddComment(e) {
        e.preventDefault();
        
        const input = document.getElementById('comment-input');
        const comment = input.value.trim();
        
        if (!comment) return;
        
        fetch(`/community/posts/${currentPostId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ comment })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadComments(currentPostId);
                
                // Update comment count in post card
                const postCard = document.querySelector(`[data-post-id="${currentPostId}"]`);
                if (postCard) {
                    const commentBtn = postCard.querySelector('.comment-btn span:last-child');
                    if (commentBtn) {
                        const count = parseInt(commentBtn.textContent) + 1;
                        commentBtn.textContent = count;
                    }
                }
            } else {
                showToast('Error', data.message || 'Failed to add comment', 'error');
            }
        })
        .catch(error => {
            console.error('Error adding comment:', error);
            showToast('Error', 'An error occurred while adding your comment', 'error');
        });
    }

    function loadCommunityPosts() {
        console.log('Loading community posts...');
        const container = document.getElementById('posts-container');
        
        if (!container) {
            console.error('Posts container not found!');
            return;
        }
        
        fetch('/community/posts', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Posts response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Posts data received:', data);
            if (data.success && data.posts && data.posts.data) {
                console.log(`Found ${data.posts.data.length} posts`);
                if (data.posts.data.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 mb-4">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg mb-2">No posts yet</p>
                            <p class="text-gray-400">Be the first to share something with the community!</p>
                        </div>
                    `;
                } else {
                    container.innerHTML = '';
                    data.posts.data.forEach(post => {
                        const postCard = createPostCard(post);
                        container.appendChild(postCard);
                    });
                }
            } else {
                console.error('Unexpected response format:', data);
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            container.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg mb-2">Failed to load posts</p>
                    <p class="text-gray-400 text-sm mb-3">${error.message}</p>
                    <button onclick="loadCommunityPosts()" class="text-emerald-600 hover:text-emerald-700 font-medium">Try again</button>
                </div>
            `;
        });
    }

    // Helper function to get initials from name
    function getInitials(name) {
        if (!name) return '?';
        const words = name.trim().split(' ');
        if (words.length === 1) {
            return words[0].substring(0, 2).toUpperCase();
        }
        return (words[0][0] + words[words.length - 1][0]).toUpperCase();
    }
    
    // Helper function to create avatar HTML (with initials fallback)
    function createAvatarHtml(user, size = 'w-10 h-10', textSize = 'text-sm') {
        const name = user?.display_name || user?.organization_name || user?.name || 'Unknown';
        const avatarUrl = user?.profile_picture_url;
        
        // Check if avatar URL is valid and not a default/placeholder
        const hasValidAvatar = avatarUrl && 
                               avatarUrl.trim() !== '' && 
                               avatarUrl !== '/images/default-avatar.png' &&
                               avatarUrl !== 'default-avatar.png' &&
                               !avatarUrl.includes('default');
        
        if (hasValidAvatar) {
            return `<img src="${avatarUrl}" alt="${name}" class="${size} rounded-full object-cover">`;
        } else {
            const initials = getInitials(name);
            const colors = [
                'bg-gradient-to-br from-blue-400 to-blue-600',
                'bg-gradient-to-br from-green-400 to-green-600',
                'bg-gradient-to-br from-purple-400 to-purple-600',
                'bg-gradient-to-br from-pink-400 to-pink-600',
                'bg-gradient-to-br from-indigo-400 to-indigo-600',
                'bg-gradient-to-br from-emerald-400 to-emerald-600',
            ];
            const colorIndex = name.charCodeAt(0) % colors.length;
            const colorClass = colors[colorIndex];
            
            return `<div class="${size} ${colorClass} rounded-full flex items-center justify-center text-white font-bold ${textSize}">${initials}</div>`;
        }
    }
    
    function createPostCard(post) {
        const card = document.createElement('div');
        card.className = 'bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden';
        card.dataset.postId = post.id;
        
        // Get user display name and avatar
        const userName = post.user?.display_name || post.user?.organization_name || post.user?.name || 'Unknown User';
        const avatarHtml = createAvatarHtml(post.user, 'w-12 h-12', 'text-lg');
        const isOrg = post.type === 'organization';
        const formattedDate = new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        let contentLink = '';
        if (post.trail) {
            contentLink = `<a href="/trails/${post.trail.slug}" class="text-emerald-600 hover:text-emerald-700 font-medium">📍 ${post.trail.trail_name}</a>`;
        } else if (post.event) {
            contentLink = `<a href="/events/${post.event.slug}" class="text-emerald-600 hover:text-emerald-700 font-medium">🎉 ${post.event.title}</a>`;
        }
        
        let ratingHtml = '';
        if (post.rating) {
            const stars = '⭐'.repeat(post.rating) + '☆'.repeat(5 - post.rating);
            ratingHtml = `<div class="mt-2 text-yellow-500 text-lg">${stars}</div>`;
        }
        
        let conditionsHtml = '';
        if (post.conditions && post.conditions.length > 0) {
            conditionsHtml = `
                <div class="mt-3 flex flex-wrap gap-2">
                    ${post.conditions.map(condition => `
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium capitalize">${condition}</span>
                    `).join('')}
                </div>
            `;
        }
        
        let imagesHtml = '';
        if (post.image_urls && post.image_urls.length > 0) {
            const imageGrid = post.image_urls.length === 1 ? 'grid-cols-1' : 
                             post.image_urls.length === 2 ? 'grid-cols-2' : 'grid-cols-3';
            imagesHtml = `
                <div class="mt-4 grid ${imageGrid} gap-2">
                    ${post.image_urls.map((url, idx) => `
                        <img src="${url}" alt="Post image ${idx + 1}" 
                             class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                             onclick="openImageModal('${url}')">
                    `).join('')}
                </div>
            `;
        }
        
        const likeIcon = post.is_liked_by_auth_user ? '❤️' : '🤍';
        const likeClass = post.is_liked_by_auth_user ? 'text-red-500' : 'text-gray-500';
        
        // Check if current user owns this post
        const currentUserId = {{ auth()->id() }};
        const isOwner = post.user_id === currentUserId;
        
        // Edit/Delete menu for post owner
        let ownerMenuHtml = '';
        if (isOwner) {
            ownerMenuHtml = `
                <div class="relative inline-block text-left post-menu">
                    <button type="button" class="post-menu-btn p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </button>
                    <div class="post-menu-dropdown hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <button class="edit-post-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Post
                            </button>
                            <button class="delete-post-btn w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Post
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        card.innerHTML = `
            <div class="p-6">
                <!-- Post Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        ${avatarHtml}
                        <div>
                            <h3 class="font-semibold text-gray-900">${userName}</h3>
                            <p class="text-sm text-gray-500">${formattedDate}${post.hike_date ? ' • Hiked on ' + new Date(post.hike_date).toLocaleDateString() : ''}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        ${isOrg ? '<span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Organization</span>' : ''}
                        ${ownerMenuHtml}
                    </div>
                </div>
                
                <!-- Trail/Event Link -->
                ${contentLink ? `<div class="mb-3">${contentLink}</div>` : ''}
                
                <!-- Rating -->
                ${ratingHtml}
                
                <!-- Post Content -->
                <p class="mt-3 text-gray-700 whitespace-pre-wrap">${escapeHtml(post.content)}</p>
                
                <!-- Conditions -->
                ${conditionsHtml}
                
                <!-- Images -->
                ${imagesHtml}
                
                <!-- Actions -->
                <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <button class="like-btn flex items-center gap-2 ${likeClass} hover:text-red-500 transition-colors" data-post-id="${post.id}">
                            <span class="text-xl">${likeIcon}</span>
                            <span class="like-count font-medium">${post.likes_count || 0}</span>
                        </button>
                        <button class="comment-btn flex items-center gap-2 text-gray-500 hover:text-blue-500 transition-colors" data-post-id="${post.id}">
                            <span class="text-xl">💬</span>
                            <span class="font-medium">${post.comments_count || 0}</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add event listeners for like and comment
        const likeBtn = card.querySelector('.like-btn');
        const commentBtn = card.querySelector('.comment-btn');
        
        if (likeBtn) {
            likeBtn.addEventListener('click', function() {
                toggleLike(post.id, this);
            });
        }
        
        if (commentBtn) {
            commentBtn.addEventListener('click', function() {
                // Open comments modal
                window.dispatchEvent(new CustomEvent('openCommentsModal', { detail: { postId: post.id } }));
            });
        }
        
        // Add event listeners for edit/delete menu
        if (isOwner) {
            const menuBtn = card.querySelector('.post-menu-btn');
            const menuDropdown = card.querySelector('.post-menu-dropdown');
            const editBtn = card.querySelector('.edit-post-btn');
            const deleteBtn = card.querySelector('.delete-post-btn');
            
            if (menuBtn && menuDropdown) {
                menuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menuDropdown.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!card.contains(e.target)) {
                        menuDropdown.classList.add('hidden');
                    }
                });
            }
            
            if (editBtn) {
                editBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menuDropdown.classList.add('hidden');
                    openEditModal(post);
                });
            }
            
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menuDropdown.classList.add('hidden');
                    openDeleteModal(post.id);
                });
            }
        }
        
        return card;
    }

    function toggleLike(postId, button) {
        fetch(`/community/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const likeIcon = button.querySelector('span:first-child');
                const likeCount = button.querySelector('.like-count');
                
                if (data.is_liked) {
                    likeIcon.textContent = '❤️';
                    button.classList.remove('text-gray-500');
                    button.classList.add('text-red-500');
                } else {
                    likeIcon.textContent = '🤍';
                    button.classList.remove('text-red-500');
                    button.classList.add('text-gray-500');
                }
                
                likeCount.textContent = data.likes_count;
            }
        })
        .catch(error => {
            console.error('Error toggling like:', error);
        });
    }

    // ==================== EDIT POST FUNCTIONS ====================
    function openEditModal(post) {
        postToEdit = post;
        const modal = document.getElementById('edit-post-modal');
        
        // Set post ID
        document.getElementById('edit-post-id').value = post.id;
        
        // Show content info
        const contentInfo = document.getElementById('edit-content-info');
        if (post.trail) {
            contentInfo.innerHTML = `📍 Trail: <strong>${post.trail.trail_name}</strong>`;
        } else if (post.event) {
            contentInfo.innerHTML = `🎉 Event: <strong>${post.event.title}</strong>`;
        }
        
        // Set rating if exists
        if (post.rating) {
            document.getElementById('edit-rating-section').classList.remove('hidden');
            document.querySelector(`input[name="edit_rating"][value="${post.rating}"]`).checked = true;
        } else {
            document.getElementById('edit-rating-section').classList.add('hidden');
        }
        
        // Set content
        const contentField = document.getElementById('edit-post-content');
        contentField.value = post.content;
        document.getElementById('edit-char-count').textContent = post.content.length;
        
        // Show existing images
        if (post.image_urls && post.image_urls.length > 0) {
            const existingImagesSection = document.getElementById('edit-existing-images');
            const existingImagesGrid = document.getElementById('edit-existing-images-grid');
            existingImagesSection.classList.remove('hidden');
            existingImagesGrid.innerHTML = '';
            
            post.image_urls.forEach((url, index) => {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group';
                imageDiv.innerHTML = `
                    <img src="${url}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" class="delete-existing-image absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" data-index="${index}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                existingImagesGrid.appendChild(imageDiv);
            });
            
            // Add delete listeners
            document.querySelectorAll('.delete-existing-image').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.relative').remove();
                });
            });
        } else {
            document.getElementById('edit-existing-images').classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeEditModal() {
        const modal = document.getElementById('edit-post-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset form
        document.getElementById('edit-post-form').reset();
        document.getElementById('edit-new-image-previews').innerHTML = '';
        document.getElementById('edit-new-image-previews').classList.add('hidden');
        document.getElementById('edit-char-count').textContent = '0';
        postToEdit = null;
    }
    
    function handleEditImagePreview(e) {
        const files = Array.from(e.target.files);
        const previews = document.getElementById('edit-new-image-previews');
        
        if (files.length === 0) {
            previews.classList.add('hidden');
            return;
        }
        
        previews.classList.remove('hidden');
        previews.innerHTML = '';
        
        files.slice(0, 10).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                    <button type="button" class="remove-new-image absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" data-index="${index}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                previews.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
    
    function handleEditSubmit(e) {
        e.preventDefault();
        
        const postId = document.getElementById('edit-post-id').value;
        const formData = new FormData(e.target);
        
        // Get deleted image indices
        const existingImages = document.querySelectorAll('#edit-existing-images-grid .relative');
        const deletedIndices = [];
        postToEdit.image_urls.forEach((url, index) => {
            const exists = Array.from(existingImages).some(div => {
                const img = div.querySelector('img');
                return img && img.src === url;
            });
            if (!exists) {
                deletedIndices.push(index);
            }
        });
        
        if (deletedIndices.length > 0) {
            deletedIndices.forEach(index => {
                formData.append('delete_images[]', index);
            });
        }
        
        const submitBtn = document.getElementById('submit-edit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        
        fetch(`/community/posts/${postId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                loadCommunityPosts(); // Reload posts
                
                // Show success message
                showToast('Post updated successfully!', 'success');
            } else {
                showToast(data.message || 'Failed to update post', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating post:', error);
            showToast('Failed to update post. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Post';
        });
    }
    
    // ==================== DELETE POST FUNCTIONS ====================
    function openDeleteModal(postId) {
        postToDelete = postId;
        const modal = document.getElementById('delete-post-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDeleteModal() {
        const modal = document.getElementById('delete-post-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        postToDelete = null;
    }
    
    function confirmDeletePost() {
        if (!postToDelete) return;
        
        const confirmBtn = document.getElementById('confirm-delete-btn');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Deleting...';
        
        fetch(`/community/posts/${postToDelete}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDeleteModal();
                loadCommunityPosts(); // Reload posts
                showToast('Post deleted successfully!', 'success');
            } else {
                showToast(data.message || 'Failed to delete post', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting post:', error);
            showToast('Failed to delete post. Please try again.', 'error');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Delete Post';
        });
    }
    
    // ==================== TOAST NOTIFICATION ====================
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[99999] px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // Image modal
    function openImageModal(url) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] bg-black bg-opacity-90 flex items-center justify-center p-4';
        modal.innerHTML = `
            <img src="${url}" class="max-w-full max-h-full object-contain">
            <button class="absolute top-4 right-4 text-white text-4xl hover:text-gray-300">&times;</button>
        `;
        
        modal.addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        document.body.appendChild(modal);
    }
    
    // Expose functions globally for inline handlers
    window.openImageModal = openImageModal;
    window.loadCommunityPosts = loadCommunityPosts;
    </script>
    @endpush
</x-app-layout>