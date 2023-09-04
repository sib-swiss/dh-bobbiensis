<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'manuscript_content_meta_id',
        'body_type',
        'body_value',
        'item_id',
        'motivation',
        'type',
    ];

    public function annotationSelectors()
    {
        return $this->hasMany(AnnotationSelector::class);
    }

    public function page()
    {
        return $this->belongsTo(ManuscriptContentMeta::class, 'manuscript_content_meta_id', 'id');
    }

    protected static function booted()
    {
        static::deleted(function ($annotation) {
            $annotation->annotationSelectors()->delete();
        });
    }
}
