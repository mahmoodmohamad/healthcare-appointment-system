<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    const MORPH_TYPE = 'categories';

    protected $fillable = ['name_ar', 'name_en'];

    #region Scopes

    public function scopeSearch($query, $search)
    {
        $like = "%{$search}%";
        return $query
            ->where('name_ar', 'LIKE', $like)
            ->orWhere('name_en', 'LIKE', $like);
    }

    #endregion Scopes

    #region Attributes

    #endregion Attributes

    #region Relations

    #endregion Relations
}
