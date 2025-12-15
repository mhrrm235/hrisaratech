<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'user_id',
        'approver_id',
        'letter_template_id',
        'letter_number',
        'subject',
        'content',
        'letter_type',
        'status',
        'created_date',
        'approved_date',
        'printed_date',
        'notes',
    ];

    protected $casts = [
        'created_date' => 'date',
        'approved_date' => 'datetime',
        'printed_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }

    /**
     * Get all signatures for this letter (polymorphic)
     */
    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signable');
    }
}
