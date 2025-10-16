<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * InquiryPolicy Class
 * 
 * This policy class defines authorization rules for inquiry-related actions
 * in the tourism management system. It implements role-based access control
 * to ensure users can only perform actions appropriate to their role level.
 * 
 * Authorization Rules:
 * - viewAny/view: Sales, Reservation, Operator, Admin, Administrator
 * - create/update: Sales, Admin, Administrator
 * - delete/restore/forceDelete: Admin, Administrator only
 * 
 * Features:
 * - Role-based authorization
 * - Granular permission control
 * - Soft delete support
 * - Laravel authorization integration
 */
class InquiryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any inquiries
     * 
     * Allows users with dashboard roles to view the inquiries listing.
     * This includes Sales, Reservation, Operator, Admin, and Administrator roles.
     * 
     * @param User $user The authenticated user
     * @return bool True if user can view inquiries, false otherwise
     */
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['Sales', 'Reservation', 'Operator', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can view a specific inquiry
     * 
     * Allows users with dashboard roles to view individual inquiry details.
     * Note: Additional filtering may be applied in the controller based on
     * role restrictions (e.g., Reservation/Operator can only see assigned inquiries).
     * 
     * @param User $user The authenticated user
     * @param Inquiry $inquiry The inquiry to view
     * @return bool True if user can view the inquiry, false otherwise
     */
    public function view(User $user, Inquiry $inquiry)
    {
        return $user->hasAnyRole(['Sales', 'Reservation', 'Operator', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can create new inquiries
     * 
     * Restricts inquiry creation to Sales, Admin, and Administrator roles.
     * Reservation and Operator roles cannot create new inquiries.
     * 
     * @param User $user The authenticated user
     * @return bool True if user can create inquiries, false otherwise
     */
    public function create(User $user)
    {
        return $user->hasAnyRole(['Sales', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can update an inquiry
     * 
     * Allows Sales, Admin, and Administrator roles to modify inquiries.
     * Reservation and Operator roles cannot update inquiries.
     * 
     * @param User $user The authenticated user
     * @param Inquiry $inquiry The inquiry to update
     * @return bool True if user can update the inquiry, false otherwise
     */
    public function update(User $user, Inquiry $inquiry)
    {
        return $user->hasAnyRole(['Sales', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can delete an inquiry
     * 
     * Restricts inquiry deletion to Admin and Administrator roles only.
     * This is a destructive action that should be limited to high-level users.
     * 
     * @param User $user The authenticated user
     * @param Inquiry $inquiry The inquiry to delete
     * @return bool True if user can delete the inquiry, false otherwise
     */
    public function delete(User $user, Inquiry $inquiry)
    {
        return $user->hasAnyRole(['Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can restore a soft-deleted inquiry
     * 
     * Allows Admin and Administrator roles to restore inquiries that have
     * been soft-deleted. This provides a safety mechanism for accidental deletions.
     * 
     * @param User $user The authenticated user
     * @param Inquiry $inquiry The inquiry to restore
     * @return bool True if user can restore the inquiry, false otherwise
     */
    public function restore(User $user, Inquiry $inquiry)
    {
        return $user->hasAnyRole(['Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can permanently delete an inquiry
     * 
     * Restricts permanent deletion to Admin and Administrator roles only.
     * This action completely removes the inquiry from the database and
     * cannot be undone, even with soft delete restoration.
     * 
     * @param User $user The authenticated user
     * @param Inquiry $inquiry The inquiry to permanently delete
     * @return bool True if user can permanently delete the inquiry, false otherwise
     */
    public function forceDelete(User $user, Inquiry $inquiry)
    {
        return $user->hasAnyRole(['Admin', 'Administrator']);
    }
}
