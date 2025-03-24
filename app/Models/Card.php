<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Card extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            if (empty($card->card_size_id)) {
                $smallSize = CardSize::where('name', 'Petit')->first();
                if ($smallSize) {
                    $card->card_size_id = $smallSize->id;
                }
            }
        });
    }

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

    protected $casts = [
        'id' => 'integer',
        'deleted' => 'boolean',
        'creation_date' => 'date',
        'user_id' => 'integer',
        'category_id' => 'integer',
        'card_size_id' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile();

        $this->addMediaCollection('videos')
            ->singleFile();

        $this->addMediaCollection('music')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('small')
            ->fit(Fit::Contain, 150, 150)
            ->nonQueued();

        $this->addMediaConversion('grid')
            ->fit(Fit::Crop, 400, 300)
            ->nonQueued();

        if ($media && $media->collection_name === 'videos') {
            $ffmpegPath = exec('which ffmpeg');

            if (!empty($ffmpegPath)) {
                $this->addMediaConversion('thumb')
                    ->extractVideoFrame(0)
                    ->fit(Fit::Contain, 300, 300)
                    ->nonQueued();

                $this->addMediaConversion('grid')
                    ->extractVideoFrame(1)
                    ->fit(Fit::Crop, 400, 300)
                    ->nonQueued();
            }
        }
    }

    public function getThumbnailUrl($collection = 'images', $conversion = 'thumb'): string
    {
        if ($this->hasMedia($collection)) {
            return $this->getFirstMediaUrl($collection, $conversion);
        } elseif ($this->hasMedia('music')) {
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
