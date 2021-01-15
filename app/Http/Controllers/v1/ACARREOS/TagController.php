<?php


namespace App\Http\Controllers\v1\ACARREOS;


use App\Http\Controllers\Controller;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class TagController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    protected $fractal;

    protected $service;

    protected $transformer;

}
