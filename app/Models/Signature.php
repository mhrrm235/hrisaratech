<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Signature extends Model
{
    protected $fillable = [
        'user_id',
        'signable_id',
        'signable_type',
        'signature_image',
        'signature_hash',
        'signed_date',
        'ip_address',
        'user_agent',
        'is_verified',
        'verification_token',
        'verified_at',
    ];

    protected $casts = [
        'signed_date' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Get the signer (user who signed)
     */
    public function signer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the signable model (Letter, Document, etc.)
     */
    public function signable()
    {
        return $this->morphTo();
    }

    /**
     * Get all verification records
     */
    public function verifications()
    {
        return $this->hasMany(SignatureVerification::class);
    }

    /**
     * Get the latest verification
     */
    public function latestVerification()
    {
        return $this->hasOne(SignatureVerification::class)->latest();
    }

    /**
     * Generate cryptographic signature hash
     */
    public static function generateSignatureHash($signatureData, $userId, $documentId)
    {
        $data = $signatureData . '|' . $userId . '|' . $documentId . '|' . now()->toDateTimeString();
        return hash('sha256', $data);
    }

    /**
     * Generate verification token
     */
    public static function generateVerificationToken()
    {
        return Str::random(64);
    }

    /**
     * Check if signature is still valid (not tampered)
     */
    public function isValid()
    {
        $recalculatedHash = self::generateSignatureHash(
            $this->signature_image,
            $this->user_id,
            $this->signable_id
        );
        
        return hash_equals($this->signature_hash, $recalculatedHash);
    }

    /**
     * Verify the signature
     */
    public function verify($verifiedById, $remarks = null)
    {
        $this->verifications()->create([
            'verified_by_id' => $verifiedById,
            'status' => 'verified',
            'remarks' => $remarks,
        ]);

        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return $this;
    }

    /**
     * Reject the signature
     */
    public function reject($rejectedById, $remarks = null)
    {
        $this->verifications()->create([
            'verified_by_id' => $rejectedById,
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);

        return $this;
    }
}
