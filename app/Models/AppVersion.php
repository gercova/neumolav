<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppVersion extends Model
{
    use HasFactory;

    protected $table = 'app_versions';

    protected $fillable = [
        'version',
        'version_type',
        'summary',
        'description',
        'commit_hash',
        'classes_changed',
        'methods_changed',
        'views_changed',
        'scripts_changed',
        'dependencies_changed',
        'updates',
        'stats',
        'snapshot_hash',
        'created_by',
    ];

    protected $casts = [
        'version' => 'string',
        'version_type' => 'string',
        'summary' => 'string',
        'description' => 'string',
        'commit_hash' => 'string',
        'classes_changed' => 'array',
        'methods_changed' => 'array',
        'views_changed' => 'array',
        'scripts_changed' => 'array',
        'dependencies_changed' => 'array',
        'updates' => 'array',
        'stats' => 'array',
        'snapshot_hash' => 'string',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User who published/recorded this version.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper to get the latest semver recorded in database.
     */
    public static function latestVersion(): string
    {
        $latest = static::orderByDesc('id')->value('version');

        return $latest ?: '3.3.0';
    }
}
