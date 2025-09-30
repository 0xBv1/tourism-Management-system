<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Display a listing of chat messages for an inquiry.
     */
    public function index(Inquiry $inquiry): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Block Finance role from accessing chat
            if ($user->hasRole('Finance')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Finance role users are not authorized to access chat functionality.',
                    'user_roles' => $user->roles->pluck('name'),
                    'inquiry_id' => $inquiry->id
                ], 403);
            }
            
            $this->authorize('view', $inquiry);
            
            // Apply role-based filtering using the Chat model scope
            $chats = $inquiry->chats()
                ->visibleTo($user)
                ->with(['sender', 'recipient'])
                ->orderBy('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $chats
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: ' . $e->getMessage(),
                'user_roles' => auth()->user()->roles->pluck('name'),
                'inquiry_id' => $inquiry->id
            ], 403);
        }
    }

    /**
     * Store a newly created chat message.
     */
    public function store(Request $request, Inquiry $inquiry): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Block Finance role from accessing chat
            if ($user->hasRole('Finance')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Finance role users are not authorized to access chat functionality.'
                ], 403);
            }
            
            $this->authorize('view', $inquiry);

            $userRoles = $user->roles->pluck('name')->toArray();

            // Validate request based on user role
            $validationRules = ['message' => 'required|string|max:1000'];
            
            // Sales users can specify recipient
            if (in_array('Sales', $userRoles)) {
                $validationRules['recipient_id'] = 'nullable|exists:users,id';
            }

            $request->validate($validationRules);

            $recipientId = null;

            // Determine recipient based on user role
            if (in_array('Sales', $userRoles)) {
                // Sales must choose recipient (Reservation or Operation)
                $recipientId = $request->recipient_id;
                if (!$recipientId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a recipient before sending the message.'
                    ], 422);
                }
                
                $recipient = User::find($recipientId);
                if (!$recipient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected recipient not found.'
                    ], 422);
                }
                
                $this->authorize('sendTo', [Chat::class, $recipient]);
            } elseif (in_array('Reservation', $userRoles) || in_array('Operator', $userRoles)) {
                // Reservation and Operation automatically send to Sales
                $salesUser = User::role('Sales')->first();
                if (!$salesUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Sales user found to receive the message.'
                    ], 422);
                }
                $recipientId = $salesUser->id;
            }

            $chat = Chat::create([
                'inquiry_id' => $inquiry->id,
                'sender_id' => $user->id,
                'recipient_id' => $recipientId,
                'message' => $request->message,
            ]);

            $chat->load(['sender', 'recipient']);

            // Fire the chat message sent event (with error handling)
            try {
                event(new ChatMessageSent($chat, $inquiry, $user));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error('Failed to fire ChatMessageSent event: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $chat
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: ' . $e->getMessage()
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Chat message creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    /**
     * Get available recipients for the current user.
     */
    public function getRecipients(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Block Finance role from accessing chat
            if ($user->hasRole('Finance')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Finance role users are not authorized to access chat functionality.'
                ], 403);
            }
            
            $userRoles = $user->roles->pluck('name')->toArray();
            $recipients = [];

            if (in_array('Sales', $userRoles)) {
                // Sales can send to Reservation and Operation users
                $recipients = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['Reservation', 'Operator']);
                })->select('id', 'name', 'email')->get();
            } elseif (in_array('Reservation', $userRoles) || in_array('Operator', $userRoles)) {
                // Reservation and Operation can send to Sales users
                $recipients = User::role('Sales')->select('id', 'name', 'email')->get();
            }

            return response()->json([
                'success' => true,
                'data' => $recipients
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRecipients: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading recipients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a chat message as read.
     */
    public function markAsRead(Chat $chat): JsonResponse
    {
        $user = auth()->user();
        
        // Block Finance role from accessing chat
        if ($user->hasRole('Finance')) {
            return response()->json([
                'success' => false,
                'message' => 'Finance role users are not authorized to access chat functionality.'
            ], 403);
        }
        
        $this->authorize('view', $chat->inquiry);
        
        $chat->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }

    /**
     * Mark all messages in an inquiry as read for the current user.
     */
    public function markAllAsRead(Inquiry $inquiry): JsonResponse
    {
        $user = auth()->user();
        
        // Block Finance role from accessing chat
        if ($user->hasRole('Finance')) {
            return response()->json([
                'success' => false,
                'message' => 'Finance role users are not authorized to access chat functionality.'
            ], 403);
        }
        
        $this->authorize('view', $inquiry);
        
        $inquiry->chats()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All messages marked as read'
        ]);
    }
}
