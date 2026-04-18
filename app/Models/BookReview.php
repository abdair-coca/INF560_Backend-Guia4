<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookReview extends Model
{
    /**
     * $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
     *  $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
     *  $table->unsignedTinyInteger('rating');
     *  $table->text('title');
     *  $table->text('body');
     */

    use HasFactory;
    
    protected $filleable = [
        'book_id',
        'member_id',
        'reting',
        'title',
        'body',
    ];
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * relacion: un libro puede tener muchas resenas
     */
    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
    /**
     * relacion: un miembro puede tener muchas resenas
     */
    public function members(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Member::class);
    }
}
