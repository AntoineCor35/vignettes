<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'image',
        'music',
        'video',
        'description',
        'deleted',
        'creation_date',
        'user_id',
        'category_id',
        'card_size_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'deleted' => 'boolean',
        'creation_date' => 'date',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'card_size_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cardSize(): BelongsTo
    {
        return $this->belongsTo(CardSize::class);
    }
}
