<?php


namespace App\Http\Requests\Almacenes;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreNuevoLoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->user()->can('registrar_nuevo_lote');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        try {
            $regex = Obra::query()->find(Context::getIdObra())->datosContables->FormatoCuentaRegExp;
        } catch (\Exception $e) {
            $regex = "";
        }

        return [
            'id_almacen' => ['required', 'exists:cadeco.dbo.almacenes,id_almacen'],
            'referencia' => ['required'],
            'observaciones' => ['required'],
            'items' => ['required', 'array'],
            'items.*.id_material' => ['required'],
            'items.*.monto_pagado' => ['required'],
            'items.*.monto_total' => ['required'],
            'items.*.cantidad' => ['required'],

        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }

}
