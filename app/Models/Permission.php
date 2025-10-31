<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'permission_name',
        'permission_code',
        'category'
    ];

    // No updated_at column per spec
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
