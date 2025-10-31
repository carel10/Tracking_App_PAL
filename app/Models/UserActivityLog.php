<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_log';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = ['user_id', 'activity', 'ip_address', 'user_agent', 'timestamp'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
