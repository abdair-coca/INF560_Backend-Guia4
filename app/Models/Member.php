<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Type\Integer;

class Member extends Model
{
    use HasFactory;
    /**  $table->id();
     *$table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
     *$table->string('member_code', 20)->unique();
     *$table->string('phone', 20)->nullable();
     *$table->text('address')->nullable();
     *$table->enum('membership_type', ['standard', 'premium', 'student'])->default('standard');
     *$table->date('membership_expires_at')->nullable();
     *$table->unsignedTinyInteger('max_loans')->default(3);
     *$table->boolean('is_active')->default(true);
     *$table->timestamps();
     */
    protected $fillable = [
        'user_id',
        'member_code',
        'phone',
        'address',
        'membership_type',
        'membership_expires_at',
        'max_loans',
        'is_active',
    ];

    protected $casts = [
        'membership_expires_at' => 'date',
        'max_loans' => 'integer',
        'is_active' => 'boolean',
    ];


    /** Boot: Genera member_code al crear un miembro */

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Member $member) {
            if (empty($member->member_code)) {
                $member->member_code = 'LIB-'
                    . date('Ymd') . '-'
                    . str_pad(
                        random_int(0, 9999),
                        4,
                        '0',
                        STR_PAD_LEFT
                    );
            }
        });
    }

    /**
     * Accessor: ¿la membresía está activa?
     */
    public function getIsMembershipActiveAttribute(): bool
    {
        return $this->is_active
            && ($this->membership_expires_at === null
                || $this->membership_expires_at->gte(now()));
    }
    /**
     * Relación: un miembro tiene muchos préstamos.
     */
    public function loans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Loan::class);
    }
    /**
     * Relación: préstamos activos del miembro.
     */
    public function activeLoans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }
}
