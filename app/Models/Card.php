<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Card extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'deleted',
        'creation_date',
        'user_id',
        'category_id',
        'card_size_id',
    ];

    protected $casts = [
        'deleted' => 'boolean',
        'creation_date' => 'date',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'card_size_id' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->singleFile();
        $this->addMediaCollection('videos')->singleFile();
        $this->addMediaCollection('music')->singleFile();
    }

    public function getThumbnailUrl(): string
    {
        if ($this->hasMedia('images')) {
            return $this->getFirstMediaUrl('images');
        }

        if ($this->hasMedia('music')) {
            return asset('images/audio-thumbnail.png');
        }

        return asset('images/default-thumbnail.png');
    }

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
