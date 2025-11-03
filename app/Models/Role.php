<?php

/**
 * Role Model
 * 
 * Model ini merepresentasikan tabel roles dalam database.
 * Role menentukan level akses dan permissions yang dimiliki user.
 * Sistem menggunakan hierarchy_level untuk menentukan prioritas role.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'roles';

    /**
     * Menonaktifkan timestamps karena tabel roles tidak memiliki created_at dan updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - name: Nama role (contoh: Super Admin, Admin, User)
     * - division_id: ID divisi untuk role (null jika role global)
     * - hierarchy_level: Level hierarki (0 = tertinggi, semakin besar semakin rendah)
     * - description: Deskripsi atau penjelasan tentang role
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'division_id',
        'hierarchy_level',
        'description',
    ];

    /**
     * Relasi: Role belongs to Division
     * Role dapat dikaitkan dengan divisi tertentu (opsional)
     * Jika division_id null, berarti role bersifat global
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Relasi: Role belongs to many Users
     * Satu role dapat dimiliki oleh banyak user (many-to-many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    /**
     * Relasi: Role belongs to many Permissions
     * Satu role dapat memiliki banyak permissions (many-to-many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
