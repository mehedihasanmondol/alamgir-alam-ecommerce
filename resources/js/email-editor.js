import { initCKEditor } from './ckeditor-init.js';

// Global editor instance
let editor;

// Initialize CKEditor when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor
    initCKEditor('#ckeditor', {
        wordCountContainer: '#word-count',
        placeholder: 'Compose your email content here... Use the toolbar above to format your message.'
    }).then(editorInstance => {
        editor = editorInstance;
        console.log('CKEditor initialized for email editor');
        
        // Setup change listener for stats
        editor.model.document.on('change:data', () => {
            updateStats();
            autoUpdatePreview();
        });
    });

    // Initialize subject length counter
    const subjectInput = document.getElementById('email-subject');
    if (subjectInput) {
        subjectInput.addEventListener('input', function() {
            const length = this.value.length;
            const counter = document.getElementById('subject-length');
            counter.textContent = length + '/100';
            
            // Color coding
            if (length > 50) {
                counter.classList.add('text-orange-600', 'font-semibold');
            } else {
                counter.classList.remove('text-orange-600', 'font-semibold');
            }
        });
    }
});

// Update statistics
function updateStats() {
    if (!editor) return;
    
    const text = editor.getData().replace(/<[^>]*>/g, '').trim();
    const words = text.split(/\s+/).filter(w => w.length > 0).length;
    const readTime = Math.max(1, Math.ceil(words / 200)); // Average reading speed: 200 words/minute
    
    document.getElementById('content-words').textContent = words;
    document.getElementById('read-time').textContent = readTime + ' min';
}

// Auto-update preview (debounced)
let previewTimer;
function autoUpdatePreview() {
    clearTimeout(previewTimer);
    previewTimer = setTimeout(() => {
        updatePreview();
    }, 1000);
}

// Load template
window.loadTemplate = function(type) {
    const templates = {
        welcome: {
            subject: 'Welcome to Our Store! 🎉',
            content: '<h2>Welcome to Our Community!</h2><p>Dear Valued Customer,</p><p>Thank you for joining us. We\'re thrilled to have you on board and can\'t wait to serve you!</p><p>As a welcome gift, enjoy <strong>15% OFF</strong> your first purchase with code: <strong style="color: #3b82f6;">WELCOME15</strong></p><p>Start shopping now and discover our exclusive collection of health and wellness products.</p><p>Best regards,<br>Your Team</p>'
        },
        promotion: {
            subject: '🎁 Exclusive Offer: 25% OFF Everything!',
            content: '<h2 style="color: #ef4444;">Limited Time Flash Sale!</h2><p>Hi there,</p><p>We have an <strong>amazing deal</strong> that you don\'t want to miss!</p><div style="background: #fef2f2; padding: 20px; border-left: 4px solid #ef4444; margin: 20px 0; border-radius: 8px;"><h3 style="margin: 0 0 10px 0;">Get <span style="color: #ef4444; font-size: 32px; font-weight: bold;">25% OFF</span></h3><p style="margin: 0; font-size: 18px;">Use code: <strong style="font-size: 20px; color: #dc2626; background: #fee2e2; padding: 5px 10px; border-radius: 4px;">SAVE25</strong></p></div><p>⏰ Hurry! Offer ends in <strong>48 hours</strong>.</p><p><strong>Shop now and save big!</strong></p>'
        },
        announcement: {
            subject: '📢 Important Update from Our Team',
            content: '<h2>📢 Exciting News!</h2><p>Dear Valued Customer,</p><p>We have some <strong>exciting announcements</strong> to share with you:</p><ul><li>🚀 New product launches coming soon</li><li>📞 Improved customer support hours (24/7)</li><li>📦 Enhanced shipping options with faster delivery</li><li>🎁 Exclusive rewards program</li></ul><p>We\'re constantly working to improve your experience and provide you with the best service possible.</p><p><strong>Stay tuned for more updates!</strong></p><p>Thank you for being part of our community.</p><p>Warm regards,<br>The Team</p>'
        }
    };

    if (templates[type] && editor) {
        document.getElementById('email-subject').value = templates[type].subject;
        document.getElementById('subject-length').textContent = templates[type].subject.length + '/100';
        editor.setData(templates[type].content);
        updateStats();
        updatePreview();
        
        // Show success notification
        showNotification('✅ Template loaded successfully!', 'success');
    }
}

// Update preview
window.updatePreview = function() {
    if (!editor) {
        showNotification('❌ Editor not initialized yet', 'error');
        return;
    }
    
    const subject = document.getElementById('email-subject').value;
    const content = editor.getData();
    
    if (!subject && !content) {
        document.getElementById('email-preview').innerHTML = '<p class="text-gray-400 text-center italic">Your email preview will appear here...</p>';
        return;
    }
    
    const preview = `
        <div style="border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 24px;">
            <p style="font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px 0;">Subject</p>
            <h2 style="font-size: 24px; font-weight: bold; margin: 0; color: #111827;">${subject || 'No subject'}</h2>
        </div>
        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.8; color: #374151;">
            ${content || '<p style="color: #9ca3af; font-style: italic;">No content yet...</p>'}
        </div>
    `;
    
    document.getElementById('email-preview').innerHTML = preview;
}

// Send test email
window.sendTestEmail = function() {
    const type = document.getElementById('email-type').value;
    const subject = document.getElementById('email-subject').value;
    const testEmail = document.getElementById('test-email').value;
    
    if (!editor) {
        showNotification('❌ Editor not initialized', 'error');
        return;
    }
    
    const content = editor.getData();
    
    if (!subject) {
        showNotification('⚠️ Please enter a subject', 'error');
        document.getElementById('email-subject').focus();
        return;
    }
    
    if (!content || content.trim() === '') {
        showNotification('⚠️ Please enter email content', 'error');
        editor.editing.view.focus();
        return;
    }
    
    if (!testEmail || !testEmail.includes('@')) {
        showNotification('⚠️ Please enter a valid test email address', 'error');
        document.getElementById('test-email').focus();
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    button.disabled = true;
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    fetch('/admin/email-preferences/send-test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: type,
            subject: subject,
            content: content,
            test_email: testEmail
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('✅ ' + result.message, 'success');
        } else {
            showNotification('❌ ' + result.message, 'error');
        }
    })
    .catch(error => {
        showNotification('❌ Failed to send test email', 'error');
        console.error(error);
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Copy to clipboard
window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('✅ Copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('❌ Failed to copy', 'error');
    });
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-0`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
