<?php
/**
 * User: Jesús López
 * Date: 18/12/2019
 * Time: 07:10 PM
 */

namespace App\Models\CADECO\Ventas;


use Illuminate\Database\Eloquent\Model;

class PdfFactura extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Ventas.pdf_factura';
    protected $primaryKey = 'id';
}
