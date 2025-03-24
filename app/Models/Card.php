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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            // Si aucune taille de carte n'est définie, utiliser la taille "Petit" par défaut
            if (empty($card->card_size_id)) {
                $smallSize = CardSize::where('name', 'Petit')->first();
                if ($smallSize) {
                    $card->card_size_id = $smallSize->id;
                }
            }
        });
    }

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

    /**
     * Register media collections for the card
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile();

        $this->addMediaCollection('videos')
            ->singleFile();

        $this->addMediaCollection('music')
            ->singleFile();
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
