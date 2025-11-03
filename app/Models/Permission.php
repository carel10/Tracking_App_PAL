<?php

/**
 * Permission Model
 * 
 * Model ini merepresentasikan tabel permissions dalam database.
 * Permission menentukan action spesifik yang dapat dilakukan user.
 * Permissions dikelompokkan berdasarkan module untuk memudahkan manajemen.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Menonaktifkan timestamps karena tabel permissions tidak memiliki created_at dan updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - name: Nama permission (contoh: View Users, Create Users, Edit Users)
     * - module: Modul atau kategori permission (contoh: users, roles, permissions)
     * - description: Deskripsi atau penjelasan tentang permission
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'module',
        'description',
    ];

    /**
     * Relasi: Permission belongs to many Roles
     * Satu permission dapat diberikan ke banyak role (many-to-many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}
