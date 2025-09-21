<li class="onhover-dropdown">
    <i data-feather="bell"></i>
    @if($unread_notifications->count() >0)
        <span
            class="badge badge-pill badge-primary pull-right notification-badge">{{$unread_notifications->count()}}</span>
    @endif
    <span class="dot"></span>
    <ul class="notification-dropdown onhover-show-div p-0">
        <li>Notification
            @if($unread_notifications->count() > 0 )
                <span class="badge badge-pill badge-primary pull-right">{{$unread_notifications->count()}}</span>
            @endif
        </li>
        @forelse($unread_notifications as $notification)
            <li data-notification-id="{{ $notification->id }}">
                <div class="media">
                    <div class="media-body">
                        <h6 class="mt-0">
                            <!-- <span>
                                <i class="{{ $notification->data['icon'] ?? 'fa-bell' }}" 
                                   style="color: {{ $notification->data['color'] ?? '#007bff' }}"></i>
                            </span> -->
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </h6>
                        <p class="mb-0">{{ $notification->data['message_text'] ?? $notification->data['message'] ?? 'No message' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        @if(isset($notification->data['action_url']))
                            <div class="mt-2">
                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </li>
        @empty
            <li>
                <div class="text-center py-3">
                    <i class="fa fa-bell-slash text-muted"></i>
                    <p class="mb-0 text-muted">No Unread Notifications</p>
                </div>
            </li>
        @endforelse

        <li class="txt-dark">
            <a href="javascript:void(0)">All</a> Notification</li>
    </ul>
</li>
