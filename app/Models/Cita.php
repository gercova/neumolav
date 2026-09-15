<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;

    protected $table = 'citas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_historia',
        'fecha_cita',
        'hora_cita',
        'numero_turno',
        'motivo',
        'observaciones',
        'descripcion',
        'id_estado',
    ];

    protected $dates = [
        'fecha_cita',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'fecha_cita'   => 'date:Y-m-d',
        'numero_turno' => 'integer',
        'id_estado'    => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /**
     * Relationship with Patient (History)
     */
    public function history(): BelongsTo
    {
        return $this->belongsTo(History::class, 'id_historia', 'id');
    }

    /**
     * Relationship with Status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'id_estado', 'id');
    }

    /**
     * Scope for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('fecha_cita', $date);
    }

    /**
     * Scope for FIFO order: Turn number ascending, then creation time ascending
     */
    public function scopeFifo($query)
    {
        return $query->orderBy('numero_turno', 'asc')
                     ->orderBy('created_at', 'asc');
    }
}
