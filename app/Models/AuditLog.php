<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    // Timestamps disabled (we only use created_at)
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_user_id',
        'action',
        'target_table',
        'target_id',
        'details',
        'created_at',
    ];

    protected $casts = [
        'target_id' => 'integer',
        'details'   => 'array',        // auto cast JSON <-> array
        'created_at'=> 'datetime',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * Dynamic target object accessor
     */
    public function getTargetObjectAttribute()
    {
        if (!$this->target_table || !$this->target_id) {
            return null;
        }

        try {
            switch ($this->target_table) {
                case 'users':
                    return \App\Models\User::find($this->target_id);

                case 'divisions':
                    return \App\Models\Division::find($this->target_id);

                case 'roles':
                    return \App\Models\Role::find($this->target_id);

                case 'permissions':
                    return \App\Models\Permission::find($this->target_id);

                case 'admin_scopes':
                    return \App\Models\AdminScope::with(['adminUser', 'division'])
                        ->find($this->target_id);

                default:
                    return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
