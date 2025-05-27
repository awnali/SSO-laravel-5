<?php

namespace Awnali\LaravelSsoServer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SsoBroker extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'sso_brokers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'broker_id',
        'broker_secret',
        'broker_name',
        'broker_url',
        'is_active',
        'allowed_domains',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'allowed_domains' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'broker_secret',
    ];

    /**
     * Get the sessions for this broker.
     */
    public function sessions()
    {
        return $this->hasMany(SsoSession::class, 'broker_id', 'broker_id');
    }

    /**
     * Scope a query to only include active brokers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the broker is allowed to access from the given domain.
     */
    public function isAllowedDomain(string $domain): bool
    {
        if (empty($this->allowed_domains)) {
            return true;
        }

        foreach ($this->allowed_domains as $allowedDomain) {
            if ($allowedDomain === '*' || $allowedDomain === $domain) {
                return true;
            }

            // Check for wildcard subdomains
            if (str_starts_with($allowedDomain, '*.')) {
                $pattern = str_replace('*.', '', $allowedDomain);
                if (str_ends_with($domain, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verify the broker secret.
     */
    public function verifySecret(string $secret): bool
    {
        return hash_equals($this->broker_secret, $secret);
    }
}