<?php

/**
 * Setting Model
 * 
 * Model ini merepresentasikan tabel settings dalam database.
 * Setting digunakan untuk menyimpan konfigurasi sistem secara dinamis.
 * Settings dikelompokkan berdasarkan group untuk kemudahan manajemen.
 * Mendukung berbagai tipe data: string, integer, boolean, json.
 * 
 * @package App\Models
 * @author Tracking App Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * Nama tabel yang digunakan oleh model ini
     * @var string
     */
    protected $table = 'settings';

    /**
     * Field-field yang dapat diisi secara mass assignment
     * - key: Unique key untuk setting (contoh: app_name, session_lifetime)
     * - value: Nilai dari setting (dapat berupa string, number, json)
     * - group: Kelompok setting (contoh: general, security, email)
     * - type: Tipe data value (string, integer, boolean, json)
     * - description: Deskripsi tentang setting ini
     * 
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Static method: Mendapatkan nilai setting berdasarkan key
     * 
     * Method ini mencari setting berdasarkan key dan mengembalikan nilai
     * yang sudah di-cast sesuai dengan tipe data setting tersebut.
     * Jika setting tidak ditemukan, mengembalikan default value.
     * 
     * @param  string  $key Key dari setting yang ingin diambil
     * @param  mixed  $default Nilai default jika setting tidak ditemukan
     * @return mixed Nilai setting yang sudah di-cast sesuai tipenya
     */
    public static function get($key, $default = null)
    {
        // Mencari setting berdasarkan key
        $setting = self::where('key', $key)->first();
        
        // Jika tidak ditemukan, return default
        if (!$setting) {
            return $default;
        }

        // Cast value sesuai dengan type dan return
        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Static method: Menyimpan atau update setting berdasarkan key
     * 
     * Method ini akan membuat setting baru jika key belum ada,
     * atau mengupdate setting yang sudah ada jika key sudah ada.
     * Array akan otomatis di-encode menjadi JSON.
     * 
     * @param  string  $key Key dari setting
     * @param  mixed  $value Nilai dari setting
     * @param  string  $group Kelompok setting (default: 'general')
     * @param  string  $type Tipe data value (default: 'string')
     * @return Setting Instance dari Setting model yang baru dibuat atau di-update
     */
    public static function set($key, $value, $group = 'general', $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key], // Kondisi pencarian
            [
                'value' => is_array($value) ? json_encode($value) : $value, // Encode array ke JSON
                'group' => $group,
                'type' => $type,
            ]
        );
    }

    /**
     * Protected static method: Cast value berdasarkan type
     * 
     * Method ini mengkonversi string value dari database menjadi
     * tipe data yang sesuai dengan type setting.
     * 
     * @param  mixed  $value Nilai yang akan di-cast
     * @param  string  $type Tipe data target (boolean, integer, json, string)
     * @return mixed Nilai yang sudah di-cast sesuai tipenya
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                // Convert string ke boolean (mendukung 'true', 'false', '1', '0', dll)
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                // Convert ke integer
                return (int) $value;
            case 'json':
                // Decode JSON string ke array/object
                return json_decode($value, true);
            default:
                // Default: return sebagai string
                return $value;
        }
    }

    /**
     * Static method: Mendapatkan semua settings dalam satu group
     * 
     * Method ini mengembalikan collection semua settings yang
     * termasuk dalam group tertentu.
     * 
     * @param  string  $group Nama group yang ingin diambil
     * @return \Illuminate\Database\Eloquent\Collection Collection of Setting models
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }
}

