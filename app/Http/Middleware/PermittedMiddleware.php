<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermittedMiddleware
{
    private array $excluded =[
        'dashboard.toggle-theme',
        'dashboard.cache.clear',
        'dashboard.currencies.rates.update',
        'dashboard.model.auto.translate',
        'dashboard.sitemap.generate',
        'dashboard.car-routes.template',
        'dashboard.service-approvals.update-status',
        'dashboard.hotels.calendar',
        'dashboard.vehicles.calendar',
        'dashboard.guides.calendar',
        'dashboard.representatives.calendar',
        'dashboard.debug.user-roles',
        'dashboard.inquiries.confirm-form',
        'dashboard.inquiries.process-confirmation',
        'dashboard.inquiries.update-tour-itinerary',
        'dashboard.inquiries.resources.store',
        'dashboard.inquiries.resources.show',
        'dashboard.inquiries.resources.available',
        'dashboard.inquiries.resources.destroy',
        'dashboard.chats.recipients',

    ];
    public function handle(Request $request, Closure $next)
    {
        try {
            $permission = Str::of($request->route()->getName())
                ->remove('dashboard.')
                ->replace('store', 'create')
                ->replace('index', 'list')
                ->replace('update', 'edit')
                ->replace('destroy', 'delete')
                ->replace('resource-utilization.export', 'export');

        } catch (\Exception $exception) {
            report($exception);
            $permission = '';
        }

        if ($permission && !in_array($request->route()->getName(), $this->excluded)) {
            abort_if(admin()->cannot($permission), 403);
        }

        return $next($request);
    }
}
