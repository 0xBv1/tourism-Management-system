<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;

/**
 * Base DataTable Class
 * 
 * This base class provides common functionality for all DataTables in the application,
 * including HTTPS URL handling for Replit environments.
 */
abstract class BaseDataTable extends DataTable
{
    /**
     * Get the AJAX URL for the DataTable with HTTPS enforcement for Replit
     * 
     * @return string The AJAX URL with proper protocol
     */
    protected function getAjaxUrl(): string
    {
        // Use the DataTable's built-in method to get the AJAX URL
        $url = $this->getAjaxUrlFromRoute();
        
        // Force HTTPS for Replit domains
        if (str_contains(request()->getHost(), 'replit.dev') || 
            str_contains(request()->getHost(), 'repl.co')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
    
    /**
     * Get the AJAX URL from the route
     * 
     * @return string The AJAX URL
     */
    private function getAjaxUrlFromRoute(): string
    {
        // Get the current route name
        $routeName = request()->route()->getName();
        
        // For DataTables, we typically use the same route for both page load and AJAX
        // The DataTable package handles the AJAX processing automatically
        return route($routeName);
    }
}
