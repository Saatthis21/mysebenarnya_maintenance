<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PublicUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'public_users';
    protected $primaryKey = 'id';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id',
        'user_Name',
        'user_Email',
        'user_Phone_Number',
        'user_Password',
        'user_Status',
        'user_Created_At',
        'user_Updated_At',
    ];

    protected $hidden = [
        'user_Password',
    ];

    protected $casts = [
        'user_Created_At' => 'datetime',
        'user_Updated_At' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->user_Password;
    }

    /** Extension of users: id IS the FK to users.id */
    public function userRecord()
    {
        return $this->belongsTo(UserRecord::class, 'id', 'id');
    }

    public function inquiries()
    {
        return $this->hasMany(InquirySubmissionRecord::class, 'user_ID', 'id');
    }

    public function getNameAttribute()
    {
        return $this->user_Name;
    }

    public function getEmailAttribute()
    {
        return $this->user_Email;
    }
}
