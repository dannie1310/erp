<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:16 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contabilidad;

use App\Http\Controllers\Controller;
use App\Services\CADECO\Contabilidad\CuentaGeneralService;
use App\Traits\ControllerTrait;
use Dingo\Api\Routing\Helpers;
use League\Fractal\Manager;


class CuentaGeneralController extends Controller
{
    use Helpers, ControllerTrait;

    /**
     * @var CuentaGeneralService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;
}