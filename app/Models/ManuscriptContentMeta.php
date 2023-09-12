<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ManuscriptContentMeta extends ManuscriptContent implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'manuscript_contents';

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * getTeiUrl
     *
     * @return void
     */
    public function getTeiUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        if (! isset($this->content['data']['foaf'])) {
            return null;
        }

        return json_decode(json_encode($this->content['data']['foaf']['Document'][0]))->{'@attributes'}->about;
    }

    public function getCopyrightFontSize()
    {
        $media = $this->getFirstMedia();

        if ($media->getCustomProperty('fontsize')) {
            return $media->getCustomProperty('fontsize');
        }

        [$width, $height] = getimagesize($media->getPath());

        $fontSize = 12;
        if ($width > 1500) {
            $fontSize = 24;
        } elseif ($width > 1000) {
            $fontSize = 18;
        }

        return $fontSize;
    }

    public function imageWithCopyright()
    {

        $media = $this->getFirstMedia();

        $text = $media->getCustomProperty('copyright') ? trim($media->getCustomProperty('copyright')) : '';
        if (! $text) {
            return $media->getPath();
        }

        $filePath = 'images/'.$media->id.'_'.$media->file_name;

        $storage = Storage::disk('public');
        if ($storage->exists($filePath)) {
            return $storage->path($filePath);
        }

        $lines = explode(PHP_EOL, $text);

        $heigth = 10 + $this->getCopyrightFontSize() * count($lines);
        $image = Image::make($media->getPath());

        $image->rectangle(0,
            10,
            $image->width(),
            $heigth + 5,
            function ($draw) {
                $draw->background('rgba(255, 255, 255, 0.5)');
            }
        );

        $image->text(
            $text,
            $image->width() - 10,
            $heigth,
            function ($font) {
                $font->file(resource_path('fonts/GentiumBasic-Regular.ttf'));
                $font->size($this->getCopyrightFontSize());
                $font->color('#000');
                $font->align('right');
            }
        );

        Storage::disk('public')
            ->put(
                $filePath,
                $image->encode()
            );
        Storage::setVisibility($filePath, 'public');

        return $storage->path($filePath);
    }

    public function contentHtml()
    {
        return $this->hasOne(ManuscriptContentHtml::class, 'manuscript_id', 'manuscript_id')
            ->whereIn('extension', ['html', 'htm'])
            ->whereRaw(
                "REPLACE(REPLACE(name,'.html',''),'.htm','')=?",
                [str_replace('.xml', '', $this->name)]
            );
    }

    /**
     * return array of manuscript's folio additional languages
     *
     * @return array
     */
    public function contentsTranslations()
    {

        return $this->hasMany(ManuscriptContentHtml::class, 'manuscript_id', 'manuscript_id')
            ->whereIn('extension', ['html', 'htm'])

            // this will not work in eager load (using with method)
            ->where('name', 'like', "{$this->getFolioName()}%")
            // ->where(function ($query) {
            //     // 'name', 'like', "{$this->getFolioName()}%")
            //     $query->whereColumn('name', 'like', 'name'); // str_replace('.xml', '', $this->name));
            // })

            ->where(function ($query) {
                $query->where('name', 'LIKE', '%_ENG.%')
                    ->orWhere('name', 'LIKE', '%_FRA.%')
                    ->orWhere('name', 'LIKE', '%_GER.%');
            });
    }

    /**
     * https://iiif.io/api/presentation/3.0/#53-canvas
     */
    public function canvas(): object
    {

        $items = [];
        foreach ($this->getMedia('default') as $media) {
            $getimagesize = getimagesize($media->getPath());
            $items[] = [
                'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annopage-1/anno-1"), //"https://iiif.io/api/cookbook/recipe/0009-book-1/annotation/p0001-image",
                'type' => 'Annotation',
                'motivation' => 'painting',
                'body' => [
                    //{identifier}/{region}/{size}/{rotation}/{quality}.{format}
                    'id' => route('iiif.image.requests', [$media->id, 'full', 'max', '0', 'default', 'jpg']), //"https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18/full/max/0/default.jpg",
                    'type' => 'Image',
                    'format' => $media->mime_type,
                    'height' => $getimagesize[1],
                    'width' => $getimagesize[0],
                    // 'service' => [  // https://iiif.io/api/registry/services/
                    //     [
                    //         'id' => 'https://iiif.io/api/image/3.0/example/reference/59d09e6773341f28ea166e9f3c1e674f-gallica_ark_12148_bpt6k1526005v_f18',
                    //         'type' => 'ImageService3',
                    //         'profile' => 'level1',
                    //     ],
                ],
                'target' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}"),  // 'https://iiif.io/api/cookbook/recipe/0009-book-1/canvas/p1',
            ];
        }

        $canvas = [
            'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}"),
            'type' => 'Canvas',
            'label' => ['none' => [$this->name]],
            'height' => isset($getimagesize[1]) ? $getimagesize[1] : 100,
            'width' => isset($getimagesize[0]) ? $getimagesize[0] : 100,
            'items' => [
                [
                    'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annopage-1"), //"https://iiif.io/api/cookbook/recipe/0009-book-1/page/p1/1",
                    'type' => 'AnnotationPage',
                    'items' => $items,
                ],
            ],
            'annotations' => [
                [
                    'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annotationpage.json"),
                    'type' => 'AnnotationPage',
                ],
            ],
        ];

        return (object) $canvas;
    }

    public function annotations()
    {
        return $this->hasMany(Annotation::class);
    }

    public function annotationsTranscriptions()
    {
        return $this->annotations()->whereHas('annotationSelectors', function ($query) {
            $query->where('type', 'SvgSelector')
                ->where('value', 'like', '% stroke="#4a90e2" %');
        });
    }

    public function annotationsNotes()
    {
        return $this->annotations()->whereHas('annotationSelectors', function ($query) {
            $query->where('type', 'SvgSelector')
                ->where('value', 'like', '% stroke="#50e3c2" %');
        });
    }

    // first transcriptionm, in Y order
    // then notes, in Y order
    public function annotationsSorted(): Attribute
    {

        $annotationSorted = $this->annotationsTranscriptions
            ->sortBy('y', SORT_REGULAR, false)
            ->merge(
                $this->annotationsNotes->sortBy('y', SORT_REGULAR, false)
            );

        return Attribute::make(
            get: fn () => $annotationSorted,
        );
    }

    /**
     * https://iiif.io/api/presentation/3.0/#53-canvas
     */
    public function annotationPage(): object
    {

        $annotationPage = [];
        $annotationPage['@context'] = 'http://iiif.io/api/presentation/3/context.json';
        $annotationPage['id'] = url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annotationpage.json");
        $annotationPage['type'] = 'AnnotationPage';
        $annotationPage['items'] = [];

        $source = url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}");
        foreach ($this->annotationsSorted as $annotation) {

            $selectors = [];
            foreach ($annotation->annotationSelectors as $selector) {
                $selectors[] = [
                    'type' => $selector->type,
                    'value' => $selector->value,
                ];
            }

            $annotationItem = [
                // 'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annopage-2/anno-1"),
                'id' => $annotation->item_id,
                'type' => $annotation->type,
                'motivation' => $annotation->motivation,
                'body' => [
                    'type' => $annotation->body_type,
                    // 'language' => 'de',
                    'format' => 'text/html',
                    'value' => $annotation->body_value,
                ],
                'target' => [
                    'type' => 'SpecificResource',
                    'source' => $source,
                    'selector' => $selectors,
                ],
            ];

            $annotationPage['items'][] = $annotationItem;

        }

        return (object) $annotationPage;
    }
}
