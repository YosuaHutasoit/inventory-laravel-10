<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'notif';

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'title',
        'description',
        'date'
    ];
}
