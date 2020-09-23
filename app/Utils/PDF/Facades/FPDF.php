<?php namespace Ghidev\Fpdf\Facades;

use Illuminate\Support\Facades\Facade;

class FPDF extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'FPDF'; }
}
