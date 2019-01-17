<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/01/19
 * Time: 05:09 PM
 */

namespace App\Traits;


trait DateFormatTrait
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }

    public function fromDateTime($value)
    {
        return substr(parent::fromDateTime($value), 0, -3);
    }
}