<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Community Posts') }}
                    </h2>
                    <p class="text-sm text-gray-600">Share updates and view posts from the hiking community.</p>
                </div>
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
</x-app-layout>

@push('scripts')
<script>
    let userTrails = [];
    let userEvents = [];
    let currentPostId = null;
    
    document.addEventListener('DOMContentLoaded', function() {
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
        const container = document.getElementById('posts-container');
        
        fetch('/community/posts', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.posts && data.posts.data) {
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
        
        if (avatarUrl && avatarUrl !== '/images/default-avatar.png') {
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
                    ${isOrg ? '<span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Organization</span>' : ''}
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
    
    window.openImageModal = openImageModal;
</script>
@endpush

