<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Factura;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Suministrados;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;



class ProveedorContratista extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_empresa', [1,2,3]);
        });
    }

    public function sucursales(){
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    public function suministrados(){
        return $this->hasMany(Suministrados::class, 'id_empresa', 'id_empresa');
    }

    public function transacciones(){
        return $this->hasMany(Transaccion::class, 'id_empresa', 'id_empresa');
    }

    public function facturas(){
        return $this->hasMany(Factura::class, 'id_empresa', 'id_empresa');
    }

    public function facturaRepositorio()
    {
        return $this->hasMany(FacturaRepositorio::class, 'rfc_emisor', 'rfc');
    }

    public function getPorcentajeFormatAttribute(){
        return number_format($this->porcentaje, 2, '.', ',');
    }

    public function getEmiteFacturaFormatAttribute(){
        return $this->emite_factura == 1? 'Si':'No';
    }

    public function getEsNacionalFormatAttribute(){
        return $this->es_nacional == 1? 'Si':'No';
    }

    public function actualizar($data, $id){
        if($data['rfc'] != $data['rfc_nuevo'] && $data['rfc_nuevo'] != 'XXXXXXXXXXXX' && $data['rfc_nuevo'] != 'XEXX010101000'){
            $this->where('rfc', '=', str_replace(" ","", $data['rfc_nuevo']))->count() > 0 ? abort(403, 'El proveedor / contratisa ya esta registrado.'):'';
        }
        $data['rfc'] = $data['rfc_nuevo'];
        unset($data['rfc_nuevo']);
        $this->find($id)->update($data);
    }

    public function agregarSucursal(){
        if($this->sucursales()->count() == 0){
            $this->sucursales()->create([
                'descripcion' => 'MATRIZ'
            ]);
        }
    }

    public function validarPermisos(){
        if(!auth()->user()->can('editar_proveedor_razon_social')){
            unset($this->razon_social);
        }
        if(!auth()->user()->can('editar_proveedor_rfc')){
            unset($this->rfc);
        }
        if(!auth()->user()->can('editar_proveedor_emite_factura')){
            unset($this->emite_factura);
        }
        if(!auth()->user()->can('editar_proveedor_es_nacional')){
            unset($this->es_nacional);
        }
    }

    public function validarProveedorContratistaDuplicado(){
        if($this->rfc != "XXXXXXXXXXXX" && $this->rfc != 'XEXX010101000'){
            $this->where('rfc', '=', str_replace(" ","", $this->rfc))->count() > 0 ? abort(403, 'El Proveedor / Contratisa ya esta registrado.'):'';
        }
    }

    public function validarRegistroTransaccion(){
        $cantidad = $this->transacciones()->count();
        if($cantidad > 0){
            abort(403, 'El Proveedor / Contratisa '. $this->razon_social.' no puede ser eliminado porque tiene ' . $cantidad . ' transaccion(es) asociada(s).');
        }
    }

    public function validarXmlRegistrados(){
        $cantidad = $this->facturaRepositorio->count();

        if($cantidad > 0){
            abort(403, 'El RFC del Proveedor / Contratisa '. $this->razon_social.' no puede ser editado porque tiene ' . $cantidad . ' comprobante(s) digital(es) (XML) asociado(s).');
        }

    }
}
