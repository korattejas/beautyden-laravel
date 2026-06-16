<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffUnavailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reason',
        'type',
        'status',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    public function beautician()
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    /**
     * Get human readable type
     */
    public function getTypeTextAttribute()
    {
        $types = [
            1 => 'Full Day',
            2 => 'First Half',
            3 => 'Second Half'
        ];
        return $types[$this->type] ?? 'N/A';
    }

    /**
     * Get human readable status
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected'
        ];
        return $statuses[$this->status] ?? 'Unknown';
    }
}
