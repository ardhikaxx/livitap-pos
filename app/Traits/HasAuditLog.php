<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    protected static function bootHasAuditLog()
    {
        static::created(function (Model $model) {
            $model->auditLog('created');
        });

        static::updated(function (Model $model) {
            $model->auditLog('updated');
        });

        static::deleted(function (Model $model) {
            if (!$model->isForceDeleting()) {
                $model->auditLog('deleted');
            }
        });
    }

    protected function auditLog($action)
    {
        $user = Auth::user();
        if (!$user) return;

        $changes = $this->getChanges();

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'business_id' => $this->business_id ?? null,
            'outlet_id' => $this->outlet_id ?? null,
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'old_values' => $changes['old'] ?? null,
            'new_values' => $changes['new'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function getChanges()
    {
        $original = $this->getOriginal();
        $current = $this->getAttributes();
        $old = [];
        $new = [];

        foreach ($current as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $old[$key] = $original[$key];
                $new[$key] = $value;
            }
        }

        return ['old' => $old, 'new' => $new];
    }
}
