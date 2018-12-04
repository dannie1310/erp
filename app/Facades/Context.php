<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/12/18
 * Time: 05:45 PM
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Context extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'app.context';
    }
}