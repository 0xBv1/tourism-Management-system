<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    /**
     * Boot the trait
     */
    protected static function bootHasAuditLog()
    {
        static::created(function (Model $model) {
            $model->logActivity('created');
        });

        static::updated(function (Model $model) {
            $model->logActivity('updated');
        });

        static::deleted(function (Model $model) {
            $model->logActivity('deleted');
        });
    }

    /**
     * Log an activity
     */
    public function logActivity(string $action, array $properties = [])
    {
        if (!Auth::check()) {
            return;
        }

        $logName = $this->getTable();
        $description = $this->getActivityDescription($action);
        
        $properties = array_merge($properties, [
            'model_id' => $this->getKey(),
            'model_type' => get_class($this),
            'changes' => $this->getChanges(),
        ]);

        activity($logName)
            ->performedOn($this)
            ->causedBy(Auth::user())
            ->withProperties($properties)
            ->log($description);
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription(string $action): string
    {
        $modelName = class_basename($this);
        
        return match($action) {
            'created' => "{$modelName} was created",
            'updated' => "{$modelName} was updated",
            'deleted' => "{$modelName} was deleted",
            'restored' => "{$modelName} was restored",
            default => "{$modelName} was {$action}",
        };
    }

    /**
     * Get the model's activities
     */
    public function activities()
    {
        return $this->morphMany(
            \Spatie\Activitylog\Models\Activity::class,
            'subject'
        );
    }

    /**
     * Get recent activities
     */
    public function recentActivities(int $limit = 10)
    {
        return $this->activities()
            ->latest()
            ->limit($limit)
            ->get();
    }
}
