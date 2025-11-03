<?php

/**
 * AdminScope Model
 * 
 * Model ini merepresentasikan tabel admin_scopes dalam database.
 * AdminScope digunakan untuk delegated admin - memberikan admin privileges
 * kepada user tertentu untuk divisi tertentu saja (bukan global admin).
 * Fitur ini memungkinkan pembagian tanggung jawab administrasi per divisi.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminScope extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'admin_scopes';

    /**
     * Menonaktifkan timestamps karena tabel admin_scopes tidak memiliki created_at dan updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - admin_user_id: ID user yang diberikan admin privileges
     * - division_id: ID divisi dimana user memiliki admin privileges
     * - can_manage_roles: Boolean, apakah admin dapat manage roles di divisi ini
     * - can_manage_users: Boolean, apakah admin dapat manage users di divisi ini
     * 
     * @var array
     */
    protected $fillable = [
        'admin_user_id',
        'division_id',
        'can_manage_roles',
        'can_manage_users',
    ];

    /**
     * Type casting untuk field-field tertentu
     * - can_manage_roles: Boolean
     * - can_manage_users: Boolean
     * 
     * @var array
     */
    protected $casts = [
        'can_manage_roles' => 'boolean',
        'can_manage_users' => 'boolean',
    ];

    /**
     * Relasi: AdminScope belongs to User (sebagai admin)
     * User yang memiliki admin privileges untuk divisi tertentu
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Relasi: AdminScope belongs to Division
     * Divisi dimana admin privileges berlaku
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
