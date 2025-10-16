<?php

namespace App\Enums;

/**
 * UserRole Enum
 * 
 * This enum defines all user roles in the tourism management system.
 * It provides type-safe role definitions and helper methods for role-based
 * access control and filtering.
 * 
 * Role Definitions:
 * - ADMIN: Full system access and management capabilities
 * - SALES: Can manage inquiries, tours, and client relationships
 * - RESERVATION: Limited access, can only see assigned inquiries
 * - OPERATOR: Limited access, can only see assigned inquiries
 * - FINANCE: Can view confirmed inquiries for financial processing
 * 
 * Features:
 * - Type-safe role constants
 * - Role grouping methods (dashboard, restricted)
 * - Access control helpers
 * - Role validation methods
 */
enum UserRole: string
{
    case ADMIN = 'Admin';
    case SALES = 'Sales';
    case RESERVATION = 'Reservation';
    case OPERATOR = 'Operator';
    case FINANCE = 'Finance';

    /**
     * Get roles that can access the dashboard
     * 
     * Returns an array of role values that have full dashboard access.
     * These roles can view and manage all inquiries and system features.
     * 
     * @return array Array of role values with dashboard access
     */
    public static function dashboardRoles(): array
    {
        return [
            self::ADMIN->value,
            self::SALES->value,
            self::RESERVATION->value,
            self::OPERATOR->value,
        ];
    }

    /**
     * Get roles that have restricted access to client data
     * 
     * Returns an array of role values that have limited access to sensitive
     * client information. These roles can only see inquiries assigned to them
     * and have restricted visibility to client details.
     * 
     * @return array Array of role values with restricted access
     */
    public static function restrictedRoles(): array
    {
        return [
            self::RESERVATION->value,
            self::OPERATOR->value,
        ];
    }

    /**
     * Get all role values as an array
     * 
     * Returns all role values in a simple array format. Useful for
     * validation, dropdowns, or when you need all available roles.
     * 
     * @return array Array of all role values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if this role has restricted access
     * 
     * Determines if the current role instance has restricted access to
     * client data and system features.
     * 
     * @return bool True if role has restricted access, false otherwise
     */
    public function isRestricted(): bool
    {
        return in_array($this->value, self::restrictedRoles());
    }

    /**
     * Check if this role has dashboard access
     * 
     * Determines if the current role instance has full access to the
     * dashboard functionality and can view all inquiries.
     * 
     * @return bool True if role has dashboard access, false otherwise
     */
    public function hasDashboardAccess(): bool
    {
        return in_array($this->value, self::dashboardRoles());
    }
}
