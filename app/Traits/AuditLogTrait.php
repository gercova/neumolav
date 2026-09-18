<?php

namespace App\Traits;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait AuditLogTrait {
    public static function bootAuditLogTrait(){
        
        static::created(function ($model){
            self::logAction('create', $model);
        });

        static::updated(function ($model){
            self::logAction('update', $model);
        });

        static::deleted(function ($model){
            self::logAction('delete', $model);
        });
    }

    public static function logAction($action, $model = null) {
        // During seeding or CLI commands there is no authenticated user.
        // Fall back to user_id 1 only when that record actually exists;
        // otherwise skip logging to avoid a FK constraint violation.
        $userId = Auth::id();

        if (!$userId) {
            // No session — check if user #1 exists before using it as fallback
            if (!\DB::table('users')->where('id', 1)->exists()) {
                return;
            }
            $userId = 1;
        }

        $module   = class_basename($model ?? get_called_class());
        $recordId = $model ? $model->id : null;
        $oldData  = $action === 'update' ? json_encode($model->getOriginal()) : null;
        $newData  = $action === 'delete' ? json_encode($model->getAttributes()) : null;

        AuditLog::create([
            'action'     => $action,
            'module'     => $module,
            'record_id'  => $recordId,
            'old_data'   => $oldData,
            'new_data'   => $newData,
            'user_id'    => $userId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function logModuleAccess($moduleName, $recordId = null) {
        AuditLog::create([
            'action'    => 'access',
            'module'    => $moduleName,
            'record_id' => $recordId,
            'user_id'   => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}