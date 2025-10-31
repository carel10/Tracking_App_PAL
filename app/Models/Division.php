<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $primaryKey = 'division_id';

    public $timestamps = false;

    protected $fillable = ['division_name'];
}

