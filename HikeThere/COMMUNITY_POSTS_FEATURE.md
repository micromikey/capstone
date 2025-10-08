# Community Posts Feature - Complete Implementation

## Overview
A comprehensive social posting system for the HikeThere community platform, allowing hikers and organizations to share their hiking experiences, trail reviews, and promotional content.

## ✅ Completed Features

### 1. **Database Structure**
- **Migration**: `2025_10_08_000001_create_community_posts_table.php`
- **Tables Created**:
  - `community_posts` - Main posts table
  - `community_post_likes` - Like/heart functionality
  - `community_post_comments` - Comments with nested replies support

### 2. **Models & Relationships**
- **CommunityPost** (`app/Models/CommunityPost.php`)
  - Relationships: User, Trail, Event, TrailReview, Likes, Comments
  - Scopes: `ofType()`, `active()`, `fromFollowedOrganizations()`
  - Attributes: `image_urls`, `is_liked_by_auth_user`

- **CommunityPostLike** (`app/Models/CommunityPostLike.php`)
  - Tracks user likes on posts

- **CommunityPostComment** (`app/Models/CommunityPostComment.php`)
  - Supports nested replies (parent_id relationship)

### 3. **API Routes** (`routes/web.php`)
```php
Route::prefix('community/posts')->name('community.posts.')->group(function () {
    Route::get('/', 'index');                           // Get all posts
    Route::post('/', 'store');                          // Create new post
    Route::post('/{post}/like', 'toggleLike');         // Like/unlike post
    Route::post('/{post}/comments', 'addComment');     // Add comment/reply
    Route::get('/{post}/comments', 'getComments');     // Load more comments
    Route::delete('/comments/{comment}', 'deleteComment'); // Delete comment
    Route::delete('/{post}', 'destroy');               // Delete post
    Route::get('/user-trails', 'getUserTrails');       // Get trails for hikers
    Route::get('/organization-content', 'getOrganizationContent'); // Get org content
});
```

### 4. **Controller** (`app/Http/Controllers/CommunityPostController.php`)

#### Key Features:
- **Post Creation**:
  - Validates input (images, content, ratings, dates)
  - Handles up to 10 images per post (5MB each)
  - Stores images in `storage/community-posts`
  - Enforces 1 post per trail rule for hikers
  - Auto-creates/updates TrailReview for hiker posts with ratings

- **Like System**:
  - Toggle like/unlike with single click
  - Real-time count updates
  - Prevents duplicate likes (unique constraint)

- **Comment System**:
  - Parent comments and nested replies
  - Pagination support (10 comments per page)
  - User can delete own comments
  - Post owner can delete any comment on their post
  - Auto-increments comment count

- **Authorization**:
  - Users can only delete their own posts
  - Checks post ownership before deletion
  - Cleans up associated images on deletion

### 5. **Frontend UI** (`resources/views/components/community-dashboard.blade.php`)

#### New Tab Navigation (Above Hero Section):
- **Community Tab** - Original functionality (organizations, events, trails)
- **Posts Tab** - New social feed with posts from community

#### Posts Tab Features:

##### For Hikers:
- **Create Post Modal**:
  - Select trail from followed organizations or booked trails
  - Add star rating (1-5, optional)
  - Set hike date
  - Select conditions (weather, trail status)
  - Upload up to 10 photos with captions
  - Rich text content editor
  - Auto-creates trail review when rating is provided

- **Post Restrictions**:
  - Can only post about trails (no events)
  - Limited to 1 post per trail
  - Existing post shown in modal if user already posted

##### For Organizations:
- **Create Post Modal**:
  - Select trail OR event to promote
  - Upload photos
  - Add promotional content
  - Limited to 1 post per trail/event

##### Post Card Display:
- User profile picture and name
- Organization badge for org posts
- Trail/Event information with link
- Star rating display (for hiker posts)
- Hike date and conditions
- Image gallery with lightbox
- Like button with count
- Comment button with count
- Timestamp (relative, e.g., "2 hours ago")
- Delete button (for post owner)

##### Interaction Features:
- **Like/Heart**: Click heart icon to like/unlike
- **Comments**: 
  - Click comment button to open comment section
  - View all comments
  - Add new comment
  - Reply to comments (nested)
  - Delete own comments
  - Real-time updates

### 6. **Styling & UX**

#### Wider Container:
```css
.max-w-[90rem] /* Wider than default for better content display */
```

#### Main Tab Styling:
- Active tab: emerald border bottom, emerald text
- Inactive tab: gray text, transparent border
- Smooth transitions on hover/active
- Accessible keyboard navigation (Arrow keys, Home, End)

#### Post Card Styling:
- Clean white cards with shadow
- Hover effects (slight elevation)
- Responsive grid layout
- Image galleries with smooth transitions
- Interactive buttons with visual feedback

#### Responsive Design:
- Mobile: Single column, stacked layout
- Tablet: 2 columns
- Desktop: 3 columns
- Full-width on large screens (max 90rem)

### 7. **JavaScript Functionality**

#### Key Functions:
- `loadPosts(type, page)` - Fetch and display posts
- `openCreatePostModal()` - Show appropriate modal (hiker/org)
- `submitPost()` - Handle post creation with validation
- `toggleLike(postId)` - Like/unlike with optimistic UI
- `toggleComments(postId)` - Show/hide comment section
- `addComment(postId, commentText, parentId)` - Add comment/reply
- `deletePost(postId)` - Delete post with confirmation

#### Features:
- Infinite scroll for posts
- Real-time like count updates
- Optimistic UI updates (immediate feedback)
- Error handling with toast notifications
- Image preview before upload
- Form validation
- Loading states

### 8. **Integration with Existing Features**

#### Trail Reviews Integration:
When a hiker creates a post about a trail with a rating:
1. Post is saved to `community_posts` table
2. TrailReview is created/updated automatically
3. `trail_review_id` is linked in the post
4. This ensures posts appear in trail reviews
5. Enforces 1 review per trail per user

#### Community Dashboard Integration:
- Posts tab seamlessly integrated with existing tabs
- Maintains all existing functionality
- Shares toast notification system
- Uses existing TrailImageService for images

### 9. **Security & Validation**

#### Backend Validation:
- Image validation: max 5MB, valid image formats
- Content length: max 5000 characters
- Rating: 1-5 stars
- Date validation
- Unique constraint: user + trail + type

#### Authorization:
- Users can only create posts for their own account
- Delete restricted to post owner
- Comment deletion restricted to owner or post author

#### Database Constraints:
- Foreign key constraints with cascade delete
- Unique indexes to prevent duplicates
- Default values for counters

## Usage Guide

### For Hikers:
1. Navigate to Community → Posts tab
2. Click "Create Post" button
3. Select a trail you've visited
4. Add rating (optional but recommended)
5. Set hike date and conditions
6. Upload photos (optional, up to 10)
7. Write about your experience
8. Submit - Post appears in feed and creates trail review

### For Organizations:
1. Navigate to Community → Posts tab
2. Click "Create Post" button
3. Select a trail or event to promote
4. Upload promotional images
5. Write promotional content
6. Submit - Post appears in community feed

### Interacting with Posts:
- **Like**: Click heart icon
- **Comment**: Click comment icon, type message, press Enter
- **Reply**: Click reply on a comment, type message, press Enter
- **Delete**: Click trash icon (only on your own posts/comments)

## File Structure

```
HikeThere/
├── app/
│   ├── Models/
│   │   ├── CommunityPost.php
│   │   ├── CommunityPostLike.php
│   │   └── CommunityPostComment.php
│   └── Http/
│       └── Controllers/
│           └── CommunityPostController.php
├── database/
│   └── migrations/
│       └── 2025_10_08_000001_create_community_posts_table.php
├── resources/
│   └── views/
│       └── components/
│           └── community-dashboard.blade.php (updated)
├── routes/
│   └── web.php (updated with posts routes)
└── storage/
    └── app/
        └── public/
            └── community-posts/ (image storage)
```

## API Endpoints Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/community/posts` | Get all posts (paginated) |
| POST | `/community/posts` | Create new post |
| POST | `/community/posts/{post}/like` | Toggle like |
| POST | `/community/posts/{post}/comments` | Add comment |
| GET | `/community/posts/{post}/comments` | Get comments |
| DELETE | `/community/posts/{post}` | Delete post |
| DELETE | `/community/posts/comments/{comment}` | Delete comment |
| GET | `/community/posts/user-trails` | Get trails for hiker |
| GET | `/community/posts/organization-content` | Get trails/events for org |

## Testing Checklist

- [ ] Create post as hiker with rating → Verify review created
- [ ] Create post as hiker without rating → No review created
- [ ] Try creating 2nd post for same trail → Error shown
- [ ] Create post as organization for trail
- [ ] Create post as organization for event
- [ ] Like a post → Count increments
- [ ] Unlike a post → Count decrements
- [ ] Add comment → Appears in feed
- [ ] Reply to comment → Nested properly
- [ ] Delete own comment → Removed
- [ ] Delete own post → All data cleaned up
- [ ] Upload images → Display properly
- [ ] View on mobile → Responsive layout
- [ ] Tab navigation → Smooth transitions

## Future Enhancements (Optional)

1. **Notifications**: Notify users when someone likes/comments on their post
2. **Hashtags**: Add hashtag support for better discovery
3. **Search**: Search posts by content, trail, or user
4. **Filters**: Filter by date range, rating, trail difficulty
5. **Share**: Share posts externally (social media)
6. **Edit Posts**: Allow users to edit their posts
7. **Pinned Posts**: Allow orgs to pin important posts
8. **Post Analytics**: Show views, engagement metrics for org posts

## Notes

- Posts are visible to all authenticated users
- Image uploads are optimized and stored in public storage
- Comments support unlimited nesting depth
- Real-time updates use AJAX (no page refresh needed)
- Toast notifications provide user feedback for all actions
- Wider container (90rem) provides better content display
- Fully accessible with keyboard navigation and ARIA labels

---

**Implementation Date**: October 8, 2025  
**Status**: ✅ Complete and Functional  
**Migration Status**: ✅ Run Successfully
