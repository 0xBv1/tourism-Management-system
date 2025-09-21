<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fa fa-comments"></i> Chat Messages
            @if($inquiry->status->value === 'confirmed')
                <span class="badge badge-warning ms-2">
                    <i class="fa fa-lock"></i> Closed
                </span>
            @else
                <span class="badge badge-primary ms-2" id="unread-count">0</span>
            @endif
        </h6>
    </div>
    <div class="card-body">
        <!-- Chat Messages Container -->
        <div id="chat-messages" class="chat-messages" style="height: 400px; overflow-y: auto; border: 1px solid #e9ecef; padding: 15px; margin-bottom: 15px; background-color: #f8f9fa;">
            @if($inquiry->status->value === 'confirmed')
                <div class="text-center text-muted py-4">
                    <i class="fa fa-lock fa-2x mb-2"></i>
                    <p class="mb-0">Chat has been closed for this confirmed inquiry</p>
                    <small>Previous messages are shown below for reference</small>
                </div>
            @endif
            <!-- Messages will be loaded here via AJAX -->
        </div>

        @if(admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
            @if($inquiry->status->value === 'confirmed')
                <!-- Chat Disabled for Confirmed Inquiries -->
                <div class="alert alert-warning">
                    <i class="fa fa-lock"></i> 
                    <strong>Chat Disabled:</strong> This inquiry has been confirmed and chat is no longer available.
                    <br><small class="text-muted">Chat is automatically disabled once an inquiry reaches confirmed status.</small>
                </div>
            @else
                <!-- Chat Input Form -->
                <form id="chat-form" class="d-flex">
                    @csrf
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
                </form>

                <!-- Mark All as Read Button -->
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="mark-all-read-btn">
                        <i class="fa fa-check-double"></i> Mark All as Read
                    </button>
                </div>
            @endif
        @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> You don't have permission to participate in this chat.
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
                    <span class="badge badge-success ms-2 read-status" style="display: none;">Read</span>
                </div>
                <div class="message-content p-2 rounded" style="background-color: white; border: 1px solid #dee2e6;">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inquiryId = {{ $inquiry->id }};
    const currentUserId = {{ auth()->id() }};
    const inquiryStatus = '{{ $inquiry->status->value }}';
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('chat-message');
    const sendBtn = document.getElementById('send-btn');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    const unreadCount = document.getElementById('unread-count');
    const messageTemplate = document.getElementById('message-template');

    // State management
    let isLoading = false;
    let lastMessageCount = 0;
    let pollInterval = null;
    let isPageVisible = true;

    // Check if chat is disabled for confirmed inquiries
    if (inquiryStatus === 'confirmed') {
        // Disable all chat functionality for confirmed inquiries
        if (chatForm) {
            chatForm.style.display = 'none';
        }
        if (markAllReadBtn) {
            markAllReadBtn.style.display = 'none';
        }
        // Still load messages for viewing but disable polling
        loadMessages();
        return; // Exit early for confirmed inquiries
    }

    // Load messages on page load
    loadMessages();

    // Auto-scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
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

        messageDiv.setAttribute('data-message-id', message.id);
        senderName.textContent = message.sender.name;
        messageTime.textContent = new Date(message.created_at).toLocaleString();
        messageContent.textContent = message.message;

        // Style based on sender
        if (message.sender_id === currentUserId) {
            messageDiv.classList.add('own-message');
            messageContent.style.backgroundColor = '#007bff';
            messageContent.style.color = 'white';
        }

        // Show read status
        if (message.read_at) {
            readStatus.style.display = 'inline';
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

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

        fetch(`/dashboard/inquiries/${inquiryId}/chats`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
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
</style>
