<?php

/**
 * User Model
 * 
 * Model ini merepresentasikan tabel users dalam database.
 * User adalah entitas utama dalam sistem untuk autentikasi dan otorisasi.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'users';

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - full_name: Nama lengkap user
     * - email: Alamat email untuk login
     * - password_hash: Hash dari password user (tidak disimpan plain text)
     * - division_id: ID divisi tempat user bekerja
     * - status: Status user (active, inactive, suspended)
     * - sso_subject: Subject ID untuk SSO authentication (opsional)
     * - sso_issuer: Issuer untuk SSO authentication (opsional)
     * 
     * @var array
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'division_id',
        'status',
        'sso_subject',
        'sso_issuer',
    ];

    /**
     * Mendapatkan password untuk autentikasi Laravel
     * Method ini digunakan oleh Laravel Auth untuk memverifikasi password
     * 
     * @return string Hash password dari database
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relasi: User belongs to Division
     * Setiap user memiliki satu divisi tempat mereka bekerja
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Relasi: User has many AuthSessions
     * User dapat memiliki banyak sesi aktif (multiple device login)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authSessions()
    {
        return $this->hasMany(AuthSession::class, 'user_id');
    }

    /**
     * Relasi: User has many AuditLogs (sebagai actor)
     * Mencatat semua aktivitas yang dilakukan oleh user ini
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    /**
     * Relasi: User has many AdminScopes
     * User dapat memiliki beberapa scope admin untuk divisi tertentu
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminScopes()
    {
        return $this->hasMany(AdminScope::class, 'admin_user_id');
    }

    /**
     * Relasi: User belongs to many Roles
     * User dapat memiliki beberapa role sekaligus (many-to-many)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
}
