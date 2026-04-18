<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = [
        'book_id',
        'member_id',
        'loaned_by',
        'loan_date',
        'due_date',
        'returned_date',
        'status',
        'notes',
    ];
    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'returned_date' => 'date',
    ];
    /**
     * Accessor: ¿el préstamo está vencido?
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->returned_date === null
            && $this->due_date->lt(now());
    }
    /**
     * Accessor: días restantes (negativo si vencido).
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->returned_date !== null) {
            return 0;
        }
        return (int) $this->due_date->diffInDays(
            now(),
            false
        );
    }
    /**
     * Relación: un préstamo pertenece a un miembro.
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relación: el préstamo fue realizado por un bibliotecario.
     */
    public function librarian(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'loaned_by');
    }
    /**
     * Relación: un préstamo pertenece a un libro.
     */
    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relacion: un prestamo puede tener muchas multas
     */
    public function fines(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
