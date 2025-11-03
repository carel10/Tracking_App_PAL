<?php

/**
 * AuthSession Model
 * 
 * Model ini merepresentasikan tabel auth_sessions dalam database.
 * AuthSession digunakan untuk tracking semua sesi aktif user dalam sistem.
 * Sistem mendukung multiple active sessions per user (multi-device login).
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthSession extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'auth_sessions';

    /**
     * Menonaktifkan timestamps karena menggunakan issued_at dan expires_at manual
     * @var bool
     */
    public $timestamps = false;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - user_id: ID user yang memiliki sesi ini
     * - issued_at: Waktu sesi dibuat/issued
     * - expires_at: Waktu sesi akan expire
     * - ip_address: IP address dari device yang menggunakan sesi ini
     * - user_agent: User agent string dari browser/client
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',
        'issued_at',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    /**
     * Type casting untuk field-field tertentu
     * - issued_at: DateTime
     * - expires_at: DateTime
     * 
     * @var array
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relasi: AuthSession belongs to User
     * Setiap sesi dimiliki oleh satu user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
