<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $primaryKey = 'role_id';
    
    protected $fillable = [
        'role_name',
        'role_description'
    ];

    // Disable updated_at timestamp
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
