<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Manuscript extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
    ];

    public function partners()
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'partners');
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContent>
     */
    public function contents()
    {
        return $this->hasMany(ManuscriptContent::class);
    }

    /**
     * The model's time entries.
     *
     * @return HasMany<ManuscriptContentMeta>
     */
    public function folios()
    {
        return $this->hasMany(ManuscriptContentMeta::class)->where('extension', 'xml')->orderBy('name');
    }

    /**
     * return array of manuscript's html contents
     *
     * @return HasMany<ManuscriptContentHtml>
     */
    public function contentsHtml()
    {
        return $this->hasMany(ManuscriptContentHtml::class)
            ->whereIn('extension', ['html', 'htm'])
            ->orderBy('name');
    }

    // /**
    //  * The model's time entries.
    //  *
    //  */
    // public function images()
    // {
    //     return $this->folios->each->getFirstMedia();
    // }

    public function getDisplayname(): string
    {
        return $this->getMeta('bibliographicCitation') ?: $this->name;
    }

    public function getMeta(string $key): ?string
    {

        if ($key === 'date') {
            return '380-420 CE';
        }

        $content = is_array($this->content) ? $this->content : json_decode((string) $this->content, true);

        return isset($content[$key])
        ? $content[$key]
        : '';

    }

    public function getLangExtended(): string
    {
        return 'Latin';

        $metaLanguage = $this->getMeta('language');

        if (config("manuscript.languages.{$metaLanguage}.name")) {
            return config("manuscript.languages.{$metaLanguage}.name");
        }

        return $metaLanguage;
    }

    /**
     * Return Manuscript language code
     * ex. grc for Ancient Greek
     *
     * @return string
     */
    public function getLangCode()
    {
        $metaLanguage = $this->getMeta('language');

        return $metaLanguage;

        if (isset($this->f3->get('MR_CONFIG')->languages->{$metaLanguage})) {
            return $metaLanguage;
        }

        foreach ($this->f3->get('MR_CONFIG')->languages as $langCode => $langObj) {
            if ($langObj->name == $metaLanguage) {
                return $langCode;
            }
        }

        // not found in congig.json languages
        return null;
    }

    public function getMetas(string $key): Collection
    {
        if (! $this->content) {
            return collect([]);
        }

        return collect(is_array($this->content)
            ? $this->content
            : json_decode((string) $this->content, true));

    }

    // * https://iiif.io/api/presentation/3.0/#52-manifest
    // *
    // * Manuscrio V2.1 Ex.: https://mr-mark16.sib.swiss/api/iiif/2-1/GA05/manifest
    // * ex. https://iiif.io/api/cookbook/recipe/0009-book-1/manifest.json
    // https://iiif.io/api/presentation/3.0/#52-manifest
    public function manifest(): Attribute
    {
        $manifest = [];
        $manifest['@context'] = 'http://iiif.io/api/presentation/3/context.json';
        $manifest['type'] = 'Manifest';
        $manifest['id'] = url("/iiif/{$this->name}/manifest.json");
        // $manifest['label'] = $this->name;
        $manifest['attribution'] = 'Codex Bobbiensis G.VII.15 (VL 1).
                                    <br>
                                    <a href="https://www.beniculturali.it/" target="_blank">Ministero della Cultura.</a>
                                    <br>
                                    <a href="https://bnuto.cultura.gov.it/" target="_blank">Biblioteca Nazionale Universitaria di Torino</a>';
        $manifest['metadata'] = [];
        $creator = $this->getMeta('creator');
        if ($creator) {
            $manifest['metadata'][] = [
                'label' => ['en' => ['Author']],
                'value' => ['none' => [$creator]],
            ];
        }

        $provenance = $this->getMeta('provenance');
        if ($provenance) {
            $manifest['metadata'][] = [
                'label' => ['en' => ['Published']],
                'value' => [
                    $this->getMeta('language') => [$provenance],
                ],
            ];
        }

        $manifest['label'] = $this->name; //$this->getMeta('bibliographicCitation');

        $manifest['behavior'] = [
            'individuals',
        ];

        $items = [];
        foreach ($this->folios as $folio) {
            $manifest['items'][] = $folio->canvas();
        }

        return Attribute::make(
            get: fn () => (object) $manifest,
        );
    }
}
