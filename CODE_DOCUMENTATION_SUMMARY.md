# Code Documentation Summary

## Overview
This document provides comprehensive documentation for all functions and methods in the tourism management system's core inquiry handling components.

## Files Documented

### 1. InquiryDataTable.php
**Purpose**: Handles DataTable functionality for inquiry management
**Key Methods**:
- `dataTable()`: Processes and formats inquiry data with role-based restrictions
- `query()`: Builds filtered queries based on user roles
- `html()`: Configures DataTable HTML structure and export features
- `getColumns()`: Defines table columns and their properties
- `filename()`: Generates unique filenames for exports
- `getStatusColor()`: Maps status values to Bootstrap color classes
- `formatAssignedUsers()`: Formats user assignments with role badges

### 2. InquiryController.php
**Purpose**: Manages inquiry CRUD operations and business logic
**Key Methods**:
- `index()`: Displays inquiry listing with DataTable
- `create()`: Shows inquiry creation form
- `store()`: Processes new inquiry creation
- `show()`: Displays detailed inquiry view with resources
- `edit()`: Shows inquiry editing form
- `update()`: Processes inquiry updates with status change handling
- `destroy()`: Deletes inquiries permanently
- `confirm()`: Confirms inquiries with timestamp
- `showConfirmForm()`: Shows payment confirmation form
- `processConfirmation()`: Processes payment confirmation
- `updateTourItinerary()`: Updates tour itinerary (Sales role only)

### 3. UserService.php
**Purpose**: Centralized user management and role operations
**Key Methods**:
- `getUsersByRole()`: Retrieves users grouped by specified roles
- `getAllUsersWithRoles()`: Gets all users with roles preloaded
- `groupUsersByRole()`: Groups users by role (private helper)
- `getUsersBySpecificRole()`: Gets users by single role
- `hasRestrictedAccess()`: Checks if user has restricted access
- `getDashboardUsers()`: Gets dashboard users (non-restricted)

### 4. UserRole.php (Enum)
**Purpose**: Type-safe role definitions and access control
**Key Methods**:
- `dashboardRoles()`: Returns roles with full dashboard access
- `restrictedRoles()`: Returns roles with limited access
- `values()`: Returns all role values as array
- `isRestricted()`: Checks if role has restricted access
- `hasDashboardAccess()`: Checks if role has dashboard access

### 5. User.php (Model)
**Purpose**: User model with authentication and role management
**Key Methods**:
- `firstName()`: Accessor for extracting first name from full name
- `phoneWithCode()`: Accessor for phone number with fallback

### 6. InquiryPolicy.php
**Purpose**: Authorization rules for inquiry operations
**Key Methods**:
- `viewAny()`: Authorization for viewing inquiry listings
- `view()`: Authorization for viewing specific inquiries
- `create()`: Authorization for creating inquiries
- `update()`: Authorization for updating inquiries
- `delete()`: Authorization for deleting inquiries
- `restore()`: Authorization for restoring soft-deleted inquiries
- `forceDelete()`: Authorization for permanent deletion

## Role-Based Access Control

### Role Hierarchy
1. **Administrator**: Full system access
2. **Admin**: Full system access
3. **Sales**: Can create, update, and view inquiries
4. **Reservation**: Limited access, assigned inquiries only
5. **Operator**: Limited access, assigned inquiries only
6. **Finance**: Can view confirmed inquiries only

### Access Patterns
- **Dashboard Access**: Admin, Sales, Reservation, Operator, Administrator
- **Restricted Access**: Reservation, Operator (limited data visibility)
- **Creation Rights**: Sales, Admin, Administrator
- **Deletion Rights**: Admin, Administrator only

## Key Features

### DataTable Features
- Server-side processing
- Role-based filtering
- Export functionality (Excel, CSV, Print)
- Responsive design
- Custom column formatting

### Inquiry Management
- Complete CRUD operations
- Status tracking and updates
- Resource assignment
- Payment processing
- Event-driven notifications

### Security Features
- Role-based authorization
- Data access restrictions
- Soft delete support
- API token authentication

## Dependencies
- Laravel Framework
- Spatie Permission Package
- Yajra DataTables
- Laravel Sanctum
- Carbon (Date handling)

## Usage Examples

### Getting Users by Role
```php
$userService = new UserService();
$salesUsers = $userService->getUsersBySpecificRole('Sales');
$dashboardUsers = $userService->getDashboardUsers();
```

### Checking Access
```php
$user = auth()->user();
$hasAccess = $user->hasRole(UserRole::dashboardRoles());
$isRestricted = UserRole::SALES->isRestricted();
```

### Policy Authorization
```php
if ($user->can('create', Inquiry::class)) {
    // User can create inquiries
}
```

## Best Practices
1. Always use the UserService for user role operations
2. Check policies before performing sensitive operations
3. Use enums for type-safe role handling
4. Implement proper error handling for authorization failures
5. Use events for notification and downstream processing

---

**Documentation Complete**: All functions and methods have been thoroughly documented with comprehensive comments explaining their purpose, parameters, return values, and usage context.
