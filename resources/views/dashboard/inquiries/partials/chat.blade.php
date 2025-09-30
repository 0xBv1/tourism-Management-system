<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fa fa-comments"></i> Chat Messages
            <span class="badge badge-primary ms-2" id="unread-count">0</span>
        </h6>
    </div>
    <div class="card-body">
        <!-- Chat Messages Container -->
        <div id="chat-messages" class="chat-messages" style="height: 400px; overflow-y: auto; border: 1px solid #e9ecef; padding: 15px; margin-bottom: 15px; background-color: #f8f9fa;">
            <!-- Messages will be loaded here via AJAX -->
        </div>
        
        @if(!admin()->hasRole('Finance') && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operator'])))
            <!-- Chat Input Form -->
            <form id="chat-form" class="d-flex flex-column">
                @csrf
                
                <!-- Recipient Selection (only for Sales users) -->
                @if(auth()->user()->hasRole('Sales'))
                    <div class="mb-2">
                        <label for="recipient-select" class="form-label small">Send to:</label>
                        <select id="recipient-select" name="recipient_id" class="form-select form-select-sm" required>
                            <option value="">Select recipient...</option>
                        </select>
                        <small class="form-text text-muted">Choose Reservation or Operation user</small>
                    </div>
                @elseif(auth()->user()->hasAnyRole(['Reservation', 'Operator']))
                    <div class="mb-2">
                        <div class="alert alert-info py-2">
                            <small><i class="fa fa-info-circle"></i> Messages will be sent directly to Sales team</small>
                        </div>
                    </div>
                @endif
                
                <div class="d-flex">
                    <div class="flex-grow-1 me-2">
                        <textarea 
                            id="chat-message" 
                            name="message" 
                            class="form-control" 
                            rows="2" 
                            placeholder="Type your message here..." 
                            required
                            maxlength="1000"
                        ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" id="send-btn">
                        <i class="fa fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>

            <!-- Mark All as Read Button -->
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="mark-all-read-btn">
                    <i class="fa fa-check-double"></i> Mark All as Read
                </button>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> You don't have permission to participate in this chat or show content.
            </div>
        @endif
    </div>
</div>

<!-- Chat Message Template -->
<template id="message-template">
    <div class="message mb-3" data-message-id="">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                    <strong class="sender-name me-2"></strong>
                    <small class="text-muted message-time"></small>
                    <span class="badge badge-info ms-2 private-message-badge" style="display: none;">Private</span>
                    <span class="badge badge-success ms-2 read-status" style="display: none;">Read</span>
                </div>
                <div class="message-content p-2 rounded" style="background-color: white; border: 1px solid #dee2e6;">
                </div>
                <div class="recipient-info mt-1" style="display: none;">
                    <small class="text-muted">
                        <i class="fa fa-user"></i> To: <span class="recipient-name"></span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentUserRole = '{{ auth()->user()->roles->first()->name ?? "" }}';
    
   
    
    const inquiryId = {{ $inquiry->id }};
    const currentUserId = {{ auth()->id() }};
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('chat-message');
    const sendBtn = document.getElementById('send-btn');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    const unreadCount = document.getElementById('unread-count');
    const messageTemplate = document.getElementById('message-template');
    const recipientSelect = document.getElementById('recipient-select');

    // State management
    let isLoading = false;
    let lastMessageCount = 0;
    let pollInterval = null;
    let isPageVisible = true;

    // Load messages and recipients on page load
    loadMessages();
    if (recipientSelect) {
        loadRecipients();
    }

    // Auto-scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Load available recipients
    function loadRecipients() {
        if (!recipientSelect) return;
        
        fetch('/dashboard/chats/recipients', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && recipientSelect) {
                    recipientSelect.innerHTML = '<option value="">Select recipient...</option>';
                    data.data.forEach(recipient => {
                        const option = document.createElement('option');
                        option.value = recipient.id;
                        option.textContent = `${recipient.name} (${recipient.email})`;
                        recipientSelect.appendChild(option);
                    });
                    
                    // Add validation
                    recipientSelect.addEventListener('change', function() {
                        const messageInput = document.getElementById('chat-message');
                        if (this.value && messageInput.value.trim()) {
                            sendBtn.disabled = false;
                        } else {
                            sendBtn.disabled = !this.value;
                        }
                    });
                } else {
                    console.error('API Error:', data.message || 'Unknown error');
                    if (recipientSelect) {
                        recipientSelect.innerHTML = '<option value="">Error loading recipients</option>';
                        // Show error message to user
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-warning mt-2';
                        errorDiv.innerHTML = '<small><i class="fa fa-exclamation-triangle"></i> Could not load recipients. Please refresh the page.</small>';
                        recipientSelect.parentNode.appendChild(errorDiv);
                    }
                }
            })
            .catch(error => {
                console.error('Error loading recipients:', error);
                if (recipientSelect) {
                    recipientSelect.innerHTML = '<option value="">Error loading recipients</option>';
                    // Show error message to user
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.innerHTML = '<small><i class="fa fa-exclamation-triangle"></i> Network error loading recipients. Please check your connection and refresh the page.</small>';
                    recipientSelect.parentNode.appendChild(errorDiv);
                }
            });
    }

    // Load chat messages with better error handling
    function loadMessages(showLoading = false) {
        if (isLoading) {
            console.log('Already loading messages, skipping...');
            return;
        }

        isLoading = true;
        
        if (showLoading) {
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center text-muted';
            loadingDiv.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
            chatMessages.appendChild(loadingDiv);
        }

        fetch(`/dashboard/inquiries/${inquiryId}/chats`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Only update if message count changed or it's the first load
                    if (data.data.length !== lastMessageCount || lastMessageCount === 0) {
                        displayMessages(data.data);
                        lastMessageCount = data.data.length;
                        scrollToBottom();
                    }
                } else {
                    console.error('API Error:', data.message || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                // Don't show alert for polling errors, only log them
                if (showLoading) {
                    alert('Error loading messages: ' + error.message);
                }
            })
            .finally(() => {
                isLoading = false;
                // Remove loading indicator
                const loadingDiv = chatMessages.querySelector('.text-center.text-muted');
                if (loadingDiv) {
                    loadingDiv.remove();
                }
            });
    }

    // Display messages
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        let unreadCount = 0;

        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            chatMessages.appendChild(messageElement);
            
            if (!message.read_at && message.sender_id !== currentUserId) {
                unreadCount++;
            }
        });

        updateUnreadCount(unreadCount);
    }

    // Create message element
    function createMessageElement(message) {
        const template = messageTemplate.content.cloneNode(true);
        const messageDiv = template.querySelector('.message');
        const senderName = template.querySelector('.sender-name');
        const messageTime = template.querySelector('.message-time');
        const messageContent = template.querySelector('.message-content');
        const readStatus = template.querySelector('.read-status');
        const privateBadge = template.querySelector('.private-message-badge');
        const recipientInfo = template.querySelector('.recipient-info');
        const recipientName = template.querySelector('.recipient-name');

        messageDiv.setAttribute('data-message-id', message.id);
        senderName.textContent = message.sender.name;
        messageTime.textContent = new Date(message.created_at).toLocaleString();
        messageContent.textContent = message.message;

        // Style based on sender and role
        if (message.sender_id === currentUserId) {
            messageDiv.classList.add('own-message');
            messageContent.style.backgroundColor = '#007bff';
            messageContent.style.color = 'white';
        } else {
            // Different colors for different roles
            if (message.sender && message.sender.roles) {
                const senderRole = message.sender.roles[0]?.name;
                if (senderRole === 'Sales') {
                    messageContent.style.backgroundColor = '#28a745';
                    messageContent.style.color = 'white';
                } else if (senderRole === 'Reservation') {
                    messageContent.style.backgroundColor = '#ffc107';
                    messageContent.style.color = 'black';
                } else if (senderRole === 'Operator') {
                    messageContent.style.backgroundColor = '#dc3545';
                    messageContent.style.color = 'white';
                }
            }
        }

        // Show read status
        if (message.read_at) {
            readStatus.style.display = 'inline';
        }

        // Handle private messages
        if (message.recipient_id) {
            privateBadge.style.display = 'inline';
            
            // Show recipient info based on role and message context
            if (currentUserRole === 'Sales') {
                // Sales can see recipient info for all their messages
                recipientInfo.style.display = 'block';
                recipientName.textContent = message.recipient ? message.recipient.name : 'Unknown';
            } else if (message.recipient_id === currentUserId) {
                // Show sender info for messages received by current user
                recipientInfo.style.display = 'block';
                recipientName.textContent = `From: ${message.sender.name}`;
            }
        }

        return messageDiv;
    }

    // Update unread count
    function updateUnreadCount(count) {
        unreadCount.textContent = count;
        unreadCount.style.display = count > 0 ? 'inline' : 'none';
    }

    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Validate recipient selection for Sales users
        if (currentUserRole === 'Sales' && recipientSelect && !recipientSelect.value) {
            alert('Please select a recipient before sending the message.');
            return;
        }

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

        const requestData = { message: message };
        
        // Add recipient_id if Sales user selected a recipient
        if (recipientSelect && recipientSelect.value) {
            requestData.recipient_id = recipientSelect.value;
        }

        fetch(`/dashboard/inquiries/${inquiryId}/chats`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                // Reset recipient selection for Sales users
                if (recipientSelect) {
                    recipientSelect.value = '';
                }
                loadMessages(); // Reload messages to show the new one
            } else {
                alert('Error sending message: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        })
        .finally(() => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Send';
        });
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        fetch(`/dashboard/inquiries/${inquiryId}/chats/mark-all-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages(); // Reload to update read status
            }
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
        });
    });

    // Smart polling - only when page is visible and not loading
    function startPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
        
        pollInterval = setInterval(() => {
            if (isPageVisible && !isLoading) {
                loadMessages();
            }
        }, 10000); // Poll every 10 seconds instead of 5
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Page visibility detection
    document.addEventListener('visibilitychange', function() {
        isPageVisible = !document.hidden;
        
        if (isPageVisible) {
            // Page became visible, load messages immediately and start polling
            loadMessages(true);
            startPolling();
        } else {
            // Page became hidden, stop polling to save resources
            stopPolling();
        }
    });

    // Start polling when page loads
    startPolling();

    // Stop polling when page is about to unload
    window.addEventListener('beforeunload', function() {
        stopPolling();
    });

    // Real-time updates with Laravel Echo (if configured)
    @if(config('broadcasting.default') !== 'null')
    if (typeof Echo !== 'undefined') {
        Echo.private(`inquiry.${inquiryId}`)
            .listen('.message.sent', (e) => {
                // When real-time message is received, reload messages immediately
                loadMessages();
            });
    }
    @endif
});
</script>

<style>
.chat-messages {
    scrollbar-width: thin;
    scrollbar-color: #ccc #f1f1f1;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.message {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.own-message .message-content {
    background-color: #007bff !important;
    color: white !important;
}

.own-message .sender-name {
    color: #007bff;
}

/* Role-based message styling */
.message-content[style*="background-color: #28a745"] {
    border-left: 4px solid #1e7e34;
}

.message-content[style*="background-color: #ffc107"] {
    border-left: 4px solid #e0a800;
}

.message-content[style*="background-color: #dc3545"] {
    border-left: 4px solid #c82333;
}

.private-message-badge {
    background-color: #6f42c1 !important;
    color: white !important;
}

/* Form validation styling */
.form-select:invalid {
    border-color: #dc3545;
}

.form-select:valid {
    border-color: #28a745;
}
</style>
