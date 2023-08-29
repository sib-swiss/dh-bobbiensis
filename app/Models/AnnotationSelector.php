<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnotationSelector extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'annotation_id',
        'type',
        'value',
    ];

    public function annotations()
    {
        return $this->belongsTo(Annotation::class, 'annotation_id', 'id');
    }
}
