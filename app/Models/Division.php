<?php

/**
 * Division Model
 * 
 * Model ini merepresentasikan tabel divisions dalam database.
 * Division merepresentasikan divisi atau departemen dalam organisasi.
 * Setiap user dan role dapat dikaitkan dengan divisi tertentu.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'divisions';

    /**
     * Menonaktifkan timestamps karena tabel divisions tidak memiliki created_at dan updated_at
     * @var bool
     */
    public $timestamps = false;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - name: Nama divisi (contoh: IT Department, HR Department)
     * - description: Deskripsi atau penjelasan tentang divisi
     * 
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi: Division has many Users
     * Satu divisi dapat memiliki banyak user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'division_id');
    }

    /**
     * Relasi: Division has many Roles
     * Satu divisi dapat memiliki banyak role khusus divisi tersebut
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(Role::class, 'division_id');
    }

    /**
     * Relasi: Division has many AdminScopes
     * Satu divisi dapat memiliki banyak admin scope untuk user tertentu
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminScopes()
    {
        return $this->hasMany(AdminScope::class, 'division_id');
    }
}
