<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class McmcStaff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'mcmc_staff';
    protected $primaryKey = 'id';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id',
        'staff_Name',
        'staff_Email',
        'staff_Phone_Number',
        'staff_Password',
        'staff_Role',
        'staff_First_Time_Login',
        'staff_Created_At',
        'staff_Updated_At',
    ];

    protected $hidden = [
        'staff_Password',
    ];

    protected $casts = [
        'staff_First_Time_Login' => 'boolean',
        'staff_Created_At'       => 'datetime',
        'staff_Updated_At'       => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->staff_Password;
    }

    /** Extension of users: id IS the FK to users.id */
    public function userRecord()
    {
        return $this->belongsTo(UserRecord::class, 'id', 'id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'staff_ID', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(InquiryAssignment::class, 'assigned_By', 'id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'staff_ID', 'id');
    }

    public function progressRecords()
    {
        return $this->hasMany(InquiryProgress::class, 'staff_ID', 'id');
    }

    public function isFirstTimeLogin(): bool
    {
        return $this->staff_First_Time_Login === true;
    }

    public function isAdmin(): bool
    {
        return $this->staff_Role === 'admin';
    }

    public function isSupervisor(): bool
    {
        return in_array($this->staff_Role, ['admin', 'supervisor']);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('staff_Email');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('staff_Role', $role);
    }

    public function markFirstLoginCompleted(): void
    {
        $this->staff_First_Time_Login = false;
        $this->staff_Updated_At       = now();
        $this->save();
    }
}
