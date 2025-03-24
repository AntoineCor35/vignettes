<?php

namespace App\Console\Commands;

use App\Models\Card;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegenerateMediaConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:regenerate {--model= : Le modèle spécifique à régénérer (Card par défaut)} {--id= : L\'ID spécifique du modèle à régénérer} {--collection= : La collection spécifique à régénérer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Régénère toutes les conversions de médias pour les modèles spécifiés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->option('model') ?: 'Card';
        $id = $this->option('id');
        $collection = $this->option('collection');

        $this->info("Régénération des conversions de médias...");

        // Si un ID spécifique est fourni
        if ($id) {
            $this->regenerateForSpecificModel($model, $id, $collection);
            return;
        }

        // Par défaut, traitez toutes les cartes
        $query = Media::query();

        if ($model === 'Card') {
            $query->where('model_type', Card::class);
        } else {
            $this->error("Modèle non supporté: $model");
            return;
        }

        if ($collection) {
            $query->where('collection_name', $collection);
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info("Aucun média trouvé correspondant aux critères spécifiés.");
            return;
        }

        $this->info("$count médias trouvés. Début de la régénération...");
        $bar = $this->output->createProgressBar($count);

        $query->chunkById(10, function ($medias) use ($bar) {
            foreach ($medias as $media) {
                try {
                    $media->clearConversions();
                    $media->generateConversions();
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Erreur lors de la régénération du média ID {$media->id}: {$e->getMessage()}");
                }
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Régénération terminée !");
    }

    /**
     * Régénère les conversions pour un modèle spécifique
     */
    private function regenerateForSpecificModel($modelName, $id, $collection)
    {
        if ($modelName === 'Card') {
            $model = Card::find($id);

            if (!$model) {
                $this->error("Aucune carte trouvée avec l'ID: $id");
                return;
            }

            $query = Media::where('model_type', Card::class)
                ->where('model_id', $id);

            if ($collection) {
                $query->where('collection_name', $collection);
            }

            $medias = $query->get();

            if ($medias->isEmpty()) {
                $this->info("Aucun média trouvé pour cette carte.");
                return;
            }

            $this->info("Régénération de {$medias->count()} médias pour la carte ID: $id");

            foreach ($medias as $media) {
                try {
                    $this->info("Traitement du média: {$media->file_name}");
                    $media->clearConversions();
                    $media->generateConversions();
                } catch (\Exception $e) {
                    $this->error("Erreur: {$e->getMessage()}");
                }
            }

            $this->info("Régénération terminée !");
        }
    }
}
