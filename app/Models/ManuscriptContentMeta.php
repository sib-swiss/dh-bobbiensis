<?php

namespace App\Models;

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
        foreach ($this->media as $media) {
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
            'label' => ['none' => [substr($this->name, 0, -4)]],
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

    /**
     * https://iiif.io/api/presentation/3.0/#53-canvas
     */
    public function annotations(): object
    {

        $exampleJson = '{
                            "@context": "http://iiif.io/api/presentation/3/context.json",
                            "id": "https://iiif.io/api/cookbook/recipe/0306-linking-annotations-to-manifests/annotationpage.json",
                            "type": "AnnotationPage",
                            "items": [
                                        {
                                            "id": "https://iiif.io/api/cookbook/recipe/0306-linking-annotations-to-manifests/canvas-1/annopage-2/anno-1",
                                            "type": "Annotation",
                                            "motivation": "commenting",
                                            "body": {
                                                "type": "TextualBody",
                                                "language": "de",
                                                "format": "text/plain",
                                                "value": "Der Gänseliesel-Brunnen"
                                            },
                                            "target": {
                                                "type": "SpecificResource",
                                                "source": {
                                                    "id": "https://iiif.io/api/cookbook/recipe/0306-linking-annotations-to-manifests/canvas-1",
                                                    "type": "Canvas",
                                                    "partOf": [
                                                        {
                                                            "id": "https://iiif.io/api/cookbook/recipe/0306-linking-annotations-to-manifests/manifest.json",
                                                            "type": "Manifest"
                                                        }
                                                    ]
                                            },
                                            "selector": {
                                                    "type": "FragmentSelector",
                                                    "conformsTo": "http://www.w3.org/TR/media-frags/",
                                                    "value": "xywh=300,800,1200,1200"
                                                }                                            
                                            }                                        
                                        }
                                    ]
                         }';

        $annotationPage = json_decode($exampleJson);

        $annotationPage = [];
        $annotationPage['@context'] = 'http://iiif.io/api/presentation/3/context.json';
        $annotationPage['id'] = url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annotationpage.json");
        $annotationPage['type'] = 'AnnotationPage';
        $annotationPage['items'] = [];

        $annotationExample1 = [
            'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annopage-2/anno-1"),
            'type' => 'Annotation',
            'motivation' => 'commenting',
            'body' => [
                'type' => 'TextualBody',
                'language' => 'de',
                'format' => 'text/plain',
                'value' => 'Der Gänseliesel-Brunnen',
            ],
            'target' => [
                'type' => 'SpecificResource',
                'source' => [
                    'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}"),
                    'type' => 'Canvas',
                ],
                'selector' => [
                    'type' => 'FragmentSelector',
                    'conformsTo' => 'http://www.w3.org/TR/media-frags/',
                    'value' => 'xywh=300,800,1200,1200',
                ],
            ],
        ];

        $annotationPage['items'][] = $annotationExample1;
        $annotationExample2 = [
            'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}/annopage-2/anno-2"),
            'type' => 'Annotation',
            'motivation' => 'commenting',
            'body' => [
                'type' => 'TextualBody',
                'language' => 'en',
                'format' => 'text/html',
                'value' => 'test <b>number 2</b><br><i>in italic</i>',
            ],
            'target' => [
                'type' => 'SpecificResource',
                'source' => [
                    'id' => url("/iiif/{$this->manuscript->name}/canvas/p{$this->pageNumber}"),
                    'type' => 'Canvas',
                ],
                'selector' => [
                    'type' => 'FragmentSelector',
                    'conformsTo' => 'http://www.w3.org/TR/media-frags/',
                    'value' => 'xywh=1800,800,1200,1200',
                ],
            ],
        ];

        $annotationPage['items'][] = $annotationExample2;

        return (object) $annotationPage;
    }
}
