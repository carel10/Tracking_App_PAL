<?php

/**
 * AuditLog Model
 * 
 * Model ini merepresentasikan tabel audit_logs dalam database.
 * AuditLog digunakan untuk mencatat semua aktivitas penting dalam sistem
 * seperti login, logout, create, update, delete data, dll.
 * Mendukung tracking actor (user yang melakukan aksi) dan target (objek yang terpengaruh).
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     * Menonaktifkan timestamps karena hanya created_at yang digunakan
     * @var bool
     */
    public $timestamps = false;

    /**
     * Menonaktifkan updated_at karena audit log tidak pernah di-update
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - actor_user_id: ID user yang melakukan action (nullable untuk system action)
     * - action: Nama action yang dilakukan (contoh: login, logout, user_created, etc)
     * - target_table: Nama tabel yang menjadi target action (nullable)
     * - target_id: ID record di target_table yang menjadi target action (nullable)
     * - details: JSON data tambahan tentang action (IP address, user agent, dll)
     * - created_at: Waktu action dilakukan
     * 
     * @var array
     */
    protected $fillable = [
        'actor_user_id',
        'action',
        'target_table',
        'target_id',
        'details',
        'created_at',
    ];

    /**
     * Type casting untuk field-field tertentu
     * - target_id: Integer
     * - details: Array (JSON dari/to database)
     * - created_at: DateTime
     * 
     * @var array
     */
    protected $casts = [
        'target_id' => 'integer',
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: AuditLog belongs to User (sebagai actor)
     * User yang melakukan action yang tercatat di audit log
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * Accessor: Mendapatkan target object berdasarkan target_table dan target_id
     * 
     * Method ini secara dinamis mengambil objek yang menjadi target dari action
     * berdasarkan target_table dan target_id. Berguna untuk menampilkan detail
     * target di view audit log.
     * 
     * @return mixed|null Model instance dari target atau null jika tidak ditemukan
     */
    public function getTargetObjectAttribute()
    {
        // Jika target_table atau target_id tidak ada, return null
        if (!$this->target_table || !$this->target_id) {
            return null;
        }

        try {
            // Switch berdasarkan target_table untuk mendapatkan model yang tepat
            switch ($this->target_table) {
                case 'users':
                    return User::find($this->target_id);
                case 'divisions':
                    return \App\Models\Division::find($this->target_id);
                case 'roles':
                    return \App\Models\Role::find($this->target_id);
                case 'permissions':
                    return \App\Models\Permission::find($this->target_id);
                case 'admin_scopes':
                    // Untuk admin_scopes, load relasi juga untuk kemudahan akses
                    return \App\Models\AdminScope::with(['adminUser', 'division'])->find($this->target_id);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            // Jika terjadi error, return null (graceful degradation)
            return null;
        }
    }
}