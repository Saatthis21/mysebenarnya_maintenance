<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agency extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'agencies';
    protected $primaryKey = 'id';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id',
        'agency_Name',
        'agency_Type',
        'agency_Email',
        'agency_Phone',
        'agency_Password',
        'agency_First_Time_Login',
        'agency_Created_At',
        'agency_Updated_At',
    ];

    protected $hidden = [
        'agency_Password',
    ];

    protected $casts = [
        'agency_First_Time_Login' => 'boolean',
        'agency_Created_At'       => 'datetime',
        'agency_Updated_At'       => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->agency_Password;
    }

    /** Extension of users: id IS the FK to users.id */
    public function userRecord()
    {
        return $this->belongsTo(UserRecord::class, 'id', 'id');
    }

    /** Route notifications to agency_Email (not the default ->email) */
    public function routeNotificationForMail($notification = null): string
    {
        return $this->agency_Email;
    }

    public function assignments()
    {
        return $this->hasMany(InquiryAssignment::class, 'agency_ID', 'id');
    }

    public function users()
    {
        return $this->hasMany(UserRecord::class, 'agency_ID', 'id');
    }

    public function progressRecords()
    {
        return $this->hasMany(InquiryProgress::class, 'agency_ID', 'id');
    }

    public function inquiries()
    {
        return $this->hasManyThrough(
            InquirySubmissionRecord::class,
            InquiryAssignment::class,
            'agency_ID',
            'inquiry_ID',
            'id',
            'approval_ID'
        )->join('approvals', 'inquiry_assignments.approval_ID', '=', 'approvals.approval_ID');
    }

    public function isFirstTimeLogin(): bool
    {
        return $this->agency_First_Time_Login === true;
    }

    public function markFirstTimeLoginCompleted(): void
    {
        $this->agency_First_Time_Login = false;
        $this->save();
    }

    public function getFormattedAgencyTypeAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->agency_Type));
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->agency_Created_At ? $this->agency_Created_At->format('M d, Y') : 'N/A';
    }

    public function getPendingAssignmentsCount(): int
    {
        return $this->assignments()->where('assignment_Status', 'pending')->count();
    }

    public function getInProgressAssignmentsCount(): int
    {
        return $this->assignments()->where('assignment_Status', 'in_progress')->count();
    }

    public function getCompletedAssignmentsCount(): int
    {
        return $this->assignments()->where('assignment_Status', 'completed')->count();
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('agency_Email');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('agency_Type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('agency_Name', 'like', '%' . $search . '%');
    }
}
