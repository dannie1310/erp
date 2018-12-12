<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Sistema extends Model
{
    use Translatable;
    public $translationModel = 'SistemaTranslation';

    public $fillable = [
        // TODO: Write down fillables
    ];

    public $hidden = [
            // TODO: Write down hidden
        ];

    public $translatedAttributes = [
        // TODO: Write down translatable values
    ];

    public function getRules($id = null): array
    {
       return [
        //TODO write down rules
       ];
    }
}
