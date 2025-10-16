<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * UserService Class
 * 
 * This service class provides centralized user management functionality
 * for the tourism management system. It handles user role operations,
 * grouping, and access control logic.
 * 
 * Features:
 * - User role filtering and grouping
 * - Access control checks
 * - Dashboard user management
 * - Role-based user retrieval
 * 
 * Dependencies:
 * - UserRole enum for role definitions
 * - User model for database operations
 * - Spatie Permission package for role management
 */
class UserService
{
    /**
     * Get users with specific roles grouped by role
     * 
     * Retrieves users that have any of the specified roles and groups them
     * by role name. If no roles are specified, uses dashboard roles by default.
     * 
     * @param array|null $roleNames Array of role names to filter by, or null for default roles
     * @return Collection Collection grouped by role name, each containing users with that role
     */
    public function getUsersByRole(?array $roleNames = null): Collection
    {
        $roleNames = $roleNames ?? UserRole::dashboardRoles();
        
        $users = User::with('roles')
            ->whereHas('roles', function($query) use ($roleNames) {
                $query->whereIn('name', $roleNames);
            })
            ->get();
            
        return $this->groupUsersByRole($users);
    }

    /**
     * Get all users with their roles loaded
     * 
     * Retrieves all users from the database with their associated roles
     * preloaded to avoid N+1 query problems.
     * 
     * @return Collection Collection of all users with roles loaded
     */
    public function getAllUsersWithRoles(): Collection
    {
        return User::with('roles')->get();
    }

    /**
     * Group users by their roles
     * 
     * Private helper method that takes a collection of users and groups them
     * by role name. Users with multiple roles will appear in multiple groups.
     * 
     * @param Collection $users Collection of users to group
     * @return Collection Collection grouped by role name
     */
    private function groupUsersByRole(Collection $users): Collection
    {
        $usersByRole = collect();
        
        foreach($users as $user) {
            foreach($user->roles as $role) {
                if (!isset($usersByRole[$role->name])) {
                    $usersByRole[$role->name] = collect();
                }
                $usersByRole[$role->name]->push($user);
            }
        }
        
        return $usersByRole;
    }

    /**
     * Get users by a specific role
     * 
     * Retrieves all users that have the specified role. This is useful
     * when you need users from a single role rather than multiple roles.
     * 
     * @param string $roleName The name of the role to filter by
     * @return Collection Collection of users with the specified role
     */
    public function getUsersBySpecificRole(string $roleName): Collection
    {
        return User::with('roles')
            ->whereHas('roles', function($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->get();
    }

    /**
     * Check if a user has restricted access
     * 
     * Determines if a user has restricted access based on their roles.
     * Restricted roles typically have limited visibility to certain data.
     * 
     * @param User $user The user to check
     * @return bool True if user has restricted access, false otherwise
     */
    public function hasRestrictedAccess(User $user): bool
    {
        return $user->hasRole(UserRole::restrictedRoles());
    }

    /**
     * Get dashboard users (non-restricted roles)
     * 
     * Retrieves users with dashboard roles, which are roles that have
     * full access to the dashboard functionality. This excludes restricted
     * roles that have limited access.
     * 
     * @return Collection Collection of dashboard users grouped by role
     */
    public function getDashboardUsers(): Collection
    {
        return $this->getUsersByRole(UserRole::dashboardRoles());
    }
}
