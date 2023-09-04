<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use App\Models\Manuscript;
use Illuminate\Http\Request;

class AnnotationAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllByCanvasId(Request $request)
    {

        // Get Params
        $canvasId = $request->canvasId;

        $manuscripName = explode('/', $canvasId)[4];
        $manuscript = Manuscript::firstWhere('name', $manuscripName);

        if (! $manuscript) {
            return response()->json([
                'result' => 'error',
                'message' => 'Manuscript not found',
            ]);
        }

        $folioPage = explode('/', $canvasId)[6];
        $manuscriptFolio = $manuscript->folios[substr($folioPage, 1) - 1];

        if (! $manuscriptFolio) {
            return response()->json([
                'result' => 'error',
                'message' => 'Manuscript content not found',
            ]);
        }

        $annoObj = $manuscriptFolio->annotationPage();
        $annoObj->id = $canvasId;

        return response()->json($annoObj, 200, [], JSON_PRETTY_PRINT);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $annotation = $request['annotation'];

        $canvas = $annotation['canvas'];
        $data = json_decode($annotation['data']);
        $uuid = $annotation['uuid'];
        $bodyType = is_object($data->body) ? $data->body->type : '';
        $bodyValue = is_object($data->body) ? $data->body->value : '';
        $itemId = $data->id;
        $motivation = $data->motivation;
        $target = $data->target;
        $type = $data->type;

        // canvas : "http://localhost/iiif/VL 1 Mark 15:46b-16:8/canvas/p1"

        $manuscripName = explode('/', $canvas)[4];
        $manuscript = Manuscript::firstWhere('name', $manuscripName);

        if (! $manuscript) {
            return response()->json([
                'result' => 'error',
                'message' => 'Manuscript not found',
            ]);
        }

        $folioPage = explode('/', $canvas)[6];
        $manuscriptContentMeta = $manuscript->folios[substr($folioPage, 1) - 1];

        if (! $manuscriptContentMeta) {
            return response()->json([
                'result' => 'error',
                'message' => 'Manuscript content not found',
            ]);
        }

        $annotation = Annotation::create([
            'manuscript_content_meta_id' => $manuscriptContentMeta->id,
            'body_type' => $bodyType,
            'body_value' => $bodyValue,
            'item_id' => $itemId,
            'motivation' => $motivation,
            'type' => $type,
        ]);

        // $source = is_object($target) ? $target->source : $target;
        $selectors = is_object($target) ? $target->selector : null;

        if ($selectors) {
            foreach ($selectors as $selector) {
                $annotation->annotationSelectors()->create([
                    'type' => $selector->type,
                    'value' => $selector->value,
                ]);
            }
        }

        return response()->json([
            'result' => 'success',
            'annotation' => $annotation,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $annotObj = $request['annotation'];

        $data = json_decode($annotObj['data']);
        $uuid = $annotObj['uuid'];
        $bodyType = is_object($data->body) ? $data->body->type : '';
        $bodyValue = is_object($data->body) ? $data->body->value : '';
        $itemId = $data->id;
        $motivation = $data->motivation;
        $target = $data->target;
        $type = $data->type;

        Annotation::where('item_id', $uuid)->update([
            'body_type' => $bodyType,
            'body_value' => $bodyValue,
            'motivation' => $motivation,
            'type' => $type,
        ]);
        $selectors = is_object($target) ? $target->selector : null;

        $annotation = Annotation::where('item_id', $uuid)->first();
        if ($annotation != null) {
            $annotation->annotationSelectors()->delete();

            if ($selectors) {
                foreach ($selectors as $selector) {
                    $annotation->annotationSelectors()->create([
                        'type' => $selector->type,
                        'value' => $selector->value,
                    ]);
                }
            }
        }

        return response()->json([
            'result' => 'success',
            'annotation' => $annotation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $annoId = $request['annoId'];

        $annotation = Annotation::where('item_id', $annoId)->first();
        if ($annotation) {
            $annotation->delete();

            return response()->json([
                'result' => 'success',
            ]);
        }

        return response()->json([
            'result' => 'false',
        ]);
    }
}
