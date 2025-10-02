# Toast Notification Testing Guide

## 🧪 Quick Test Commands

Open the browser console on any trail page and run these commands:

### Test Individual Toast Types

```javascript
// Success Toast
showToast('success', 'Trail successfully added to favorites!', {
    link: '#',
    linkText: 'View Saved Trails'
});

// Error Toast
showToast('error', 'Unable to connect to the server', {
    details: 'Please check your internet connection and try again'
});

// Warning Toast
showToast('warning', 'You are about to delete this review', {
    title: 'Are you sure?',
    details: 'This action cannot be undone'
});

// Info Toast
showToast('info', 'Your itinerary is being built', {
    details: 'This may take a few moments'
});
```

---

## 🔄 Test Multiple Toasts (Stacking)

```javascript
// Test toast stacking
showToast('info', 'Loading data...');

setTimeout(() => {
    showToast('success', 'Profile loaded successfully');
}, 1000);

setTimeout(() => {
    showToast('success', 'Trail data loaded');
}, 2000);

setTimeout(() => {
    showToast('warning', 'Your session will expire soon');
}, 3000);
```

---

## ⏱️ Test Custom Durations

```javascript
// Short duration (2 seconds)
showToast('info', 'Quick message!', {
    duration: 2000
});

// Long duration (10 seconds)
showToast('success', 'This will stay longer', {
    duration: 10000,
    details: 'You have 10 seconds to read this'
});

// Very long (30 seconds)
showToast('warning', 'Important notice', {
    duration: 30000,
    details: 'This toast will stay for 30 seconds'
});
```

---

## 🖱️ Test Interactive Features

### Hover to Pause
```javascript
showToast('success', 'Hover over this toast to pause the timer', {
    duration: 10000,
    details: 'Move your mouse over the toast and watch the progress bar stop'
});
```

### Close Button
```javascript
showToast('info', 'Click the X button to close me manually', {
    duration: 30000,
    details: 'The close button is in the top-right corner'
});
```

### Action Link
```javascript
showToast('success', 'Trail saved successfully!', {
    link: '/profile/saved-trails',
    linkText: 'View Saved Trails',
    details: 'Click the link to navigate'
});
```

---

## 📱 Test Mobile Responsiveness

1. Open DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Select a mobile device (iPhone, iPad, etc.)
4. Run toast commands and verify:
   - Full width with margins
   - Proper stacking
   - Touch-friendly close button
   - Readable text

```javascript
// Test on mobile
showToast('success', 'Mobile test successful!', {
    details: 'This should display full width on mobile',
    link: '#',
    linkText: 'Tap Here'
});
```

---

## 🎨 Test All Toast Variants

```javascript
// Run all types in sequence
const testAllTypes = () => {
    const types = [
        { type: 'success', msg: 'Success notification example' },
        { type: 'error', msg: 'Error notification example' },
        { type: 'warning', msg: 'Warning notification example' },
        { type: 'info', msg: 'Info notification example' }
    ];
    
    types.forEach((toast, index) => {
        setTimeout(() => {
            showToast(toast.type, toast.msg, {
                details: `This is a ${toast.type} toast`,
                duration: 8000
            });
        }, index * 500);
    });
};

// Run the test
testAllTypes();
```

---

## 🔢 Stress Test (Multiple Toasts)

```javascript
// Create many toasts at once
const stressTest = () => {
    for (let i = 1; i <= 10; i++) {
        setTimeout(() => {
            const types = ['success', 'error', 'warning', 'info'];
            const type = types[Math.floor(Math.random() * types.length)];
            showToast(type, `Toast #${i}`, {
                details: 'Testing multiple toasts',
                duration: 5000
            });
        }, i * 200);
    }
};

// Run stress test
stressTest();
```

---

## 🎯 Test Specific Features

### Test Progress Bar Animation
```javascript
showToast('info', 'Watch the progress bar animate', {
    duration: 8000,
    details: 'The bar at the top should smoothly decrease'
});
```

### Test Hover Pause/Resume
```javascript
showToast('success', 'Hover test', {
    duration: 10000,
    details: 'Hover to pause, move away to resume'
});

console.log('Hover over the toast and watch the progress bar pause');
```

### Test Manual Close
```javascript
showToast('warning', 'Manual close test', {
    duration: 60000,
    details: 'Click the X button to dismiss early'
});

console.log('Click the X button to close the toast manually');
```

---

## 🔄 Test Backwards Compatibility

```javascript
// Old-style call (using viewLink)
showToast('success', 'Old style toast', {
    viewLink: '/profile/saved-trails',
    details: 'Using legacy viewLink parameter'
});

// New-style call
showToast('success', 'New style toast', {
    link: '/profile/saved-trails',
    linkText: 'View Profile',
    details: 'Using new link and linkText parameters'
});
```

---

## 🐛 Debug Mode

```javascript
// Enable detailed logging
const debugToast = (type, message, opts = {}) => {
    console.group('🍞 Toast Debug');
    console.log('Type:', type);
    console.log('Message:', message);
    console.log('Options:', opts);
    console.groupEnd();
    
    showToast(type, message, opts);
};

// Test with debug
debugToast('success', 'Debug test', {
    details: 'Check the console',
    duration: 5000
});
```

---

## 📊 Performance Test

```javascript
// Measure toast creation time
const perfTest = () => {
    console.time('Toast Creation');
    showToast('info', 'Performance test');
    console.timeEnd('Toast Creation');
};

perfTest();

// Test rapid creation
const rapidTest = () => {
    console.time('10 Toasts');
    for (let i = 0; i < 10; i++) {
        showToast('info', `Toast ${i + 1}`);
    }
    console.timeEnd('10 Toasts');
};

rapidTest();
```

---

## 🎭 Real-World Scenarios

### Scenario 1: Save Trail
```javascript
// Simulate saving a trail
console.log('Attempting to save trail...');
setTimeout(() => {
    showToast('success', 'Trail saved successfully!', {
        title: 'Saved!',
        link: '/profile/saved-trails',
        linkText: 'View Saved Trails',
        duration: 5000
    });
}, 1000);
```

### Scenario 2: Failed Review Submission
```javascript
// Simulate failed review
console.log('Submitting review...');
setTimeout(() => {
    showToast('error', 'Unable to submit review', {
        details: 'Please check your internet connection and try again',
        duration: 6000
    });
}, 1500);
```

### Scenario 3: Building Itinerary
```javascript
// Simulate itinerary building
console.log('Building itinerary...');

showToast('info', 'Building your itinerary...', {
    duration: 3000
});

setTimeout(() => {
    showToast('success', 'Itinerary created successfully!', {
        link: '/itinerary',
        linkText: 'View Itinerary',
        duration: 5000
    });
}, 3500);
```

### Scenario 4: Content Moderation Warning
```javascript
// Simulate moderation warning
showToast('warning', 'Content flagged for review', {
    details: 'Your review contains inappropriate language',
    duration: 7000
});
```

---

## ✅ Checklist for Manual Testing

Use this checklist when testing the toast system:

### Visual Tests
- [ ] Toast slides in smoothly from right
- [ ] Colored left border visible and correct color
- [ ] Icon badge displays with correct icon
- [ ] Text is readable and properly formatted
- [ ] Progress bar animates smoothly
- [ ] Close button is visible
- [ ] Shadow/elevation is appropriate
- [ ] Border radius is smooth

### Functional Tests
- [ ] Toast auto-dismisses after specified duration
- [ ] Progress bar reaches 0% before dismiss
- [ ] Close button dismisses toast immediately
- [ ] Hover pauses the auto-dismiss timer
- [ ] Mouse leave resumes the timer
- [ ] Action links work when clicked
- [ ] Multiple toasts stack properly
- [ ] Toasts remove from DOM after dismiss

### Type-Specific Tests
- [ ] Success toast shows green/emerald colors
- [ ] Error toast shows red colors
- [ ] Warning toast shows amber/yellow colors
- [ ] Info toast shows blue colors
- [ ] Each type has correct icon

### Responsive Tests
- [ ] Desktop: Toasts fixed top-right
- [ ] Mobile: Toasts full width with margins
- [ ] Toasts resize properly on window resize
- [ ] Touch interactions work on mobile

### Edge Cases
- [ ] Very long messages wrap properly
- [ ] Very short messages display correctly
- [ ] No link: Link section hidden
- [ ] No details: Details section hidden
- [ ] Custom title works
- [ ] Invalid type defaults to info

---

## 🚨 Known Issues to Test For

1. **Z-Index Conflicts**: Ensure toast appears above all content
2. **Rapid Creation**: Multiple rapid calls should stack properly
3. **Long Messages**: Very long text should wrap, not overflow
4. **Mobile Landscape**: Check orientation changes
5. **Small Screens**: Test on very small mobile screens (320px)
6. **Browser Compatibility**: Test in Chrome, Firefox, Safari, Edge

---

## 📝 Test Report Template

```
Toast Notification Test Report
Date: [DATE]
Browser: [BROWSER] [VERSION]
Device: [Desktop/Mobile] [DEVICE NAME]

✅ Passed Tests:
- 
- 
- 

❌ Failed Tests:
- 
- 
- 

⚠️ Warnings/Notes:
- 
- 
- 

Overall Status: [PASS/FAIL]
```

---

## 🎓 Best Practices for Testing

1. **Test in Private/Incognito Mode**: Avoid cache issues
2. **Clear Console**: Clear console between tests for clarity
3. **Use Real Devices**: Test on actual mobile devices, not just emulators
4. **Test Different Browsers**: Check cross-browser compatibility
5. **Test with Network Throttling**: Simulate slow connections
6. **Test with Multiple Tabs Open**: Ensure no conflicts
7. **Test After Page Reload**: Verify no state persistence issues

---

## 🔧 Troubleshooting

### Toast Not Appearing
```javascript
// Check if container exists
console.log('Container:', document.getElementById('toast-container'));

// Check if template exists
console.log('Template:', document.getElementById('toast-template'));

// Check console for errors
console.log('Check for JavaScript errors above');
```

### Progress Bar Not Animating
```javascript
// Check toast duration
showToast('info', 'Check progress bar', {
    duration: 10000  // Longer duration = easier to see
});
```

### Multiple Toasts Not Stacking
```javascript
// Force multiple toasts
for (let i = 0; i < 5; i++) {
    showToast('info', `Toast ${i + 1}`);
}

// Check container children
setTimeout(() => {
    const container = document.getElementById('toast-container');
    console.log('Active toasts:', container.children.length);
}, 500);
```

---

## 📞 Support

If you encounter issues:
1. Check browser console for errors
2. Verify the latest build was compiled (`npm run build`)
3. Clear browser cache
4. Check that the template and container elements exist
5. Ensure no JavaScript errors are preventing execution

---

*Testing guide for Enhanced Toast Notification System*
*Last updated: October 2, 2025*
