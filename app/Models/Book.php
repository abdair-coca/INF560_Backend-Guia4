<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'isbn',
        'publisher',
        'publish_year',
        'pages',
        'language',
        'description',
        'cover_url',
        'total_copies',
        'available_copies',
        'status',
        'category_id',
    ];
    protected $casts = [
        'publish_year' => 'integer',
        'pages' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
    ];

    /**
     * Accessor: ¿tiene copias disponibles?
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0;
    }
    /**
     * Decrementa copias disponibles al prestar.
     */
    public function decrementCopies(): bool
    {
        if ($this->available_copies <= 0) {
            return false;
        }
        $this->decrement('available_copies');
        $this->refresh();
        if ($this->available_copies === 0) {
            $this->update(['status' => 'unavailable']);
        }
        return true;
    }
    /**
     * Incrementa copias al devolver un libro.
     */
    public function incrementCopies(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
            $this->refresh();
            if ($this->status !== 'available') {
                $this->update(['status' => 'available']);
            }
        }
    }
    /**
     * Obtenemos el promedio de las resenas del libro
     */
    public function getAverageRatingAttribute()
    {
        return number_format($this->reviews()->avg('rating'), 2);
    }

    /**
     * Relación: un libro tiene muchos autores.
     */
    public function authors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Author::class)
            ->withPivot('role')
            ->withTimestamps();
    }
    /**
     * Relación: un libro pertenece a una categoría.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Relación: un libro tiene muchos préstamos.
     */
    public function loans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Loan::class);
    }
    /**
     * Relación: préstamos activos del libro.
     */
    public function activeLoans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }

    /**
     * Relacion: un libro tiene muchas resenas
     */
    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookReview::class);
    }
}
