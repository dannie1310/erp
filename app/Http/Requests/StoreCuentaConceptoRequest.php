<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreCuentaConceptoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_cuenta_concepto');
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

        Validator::extend('sin_cuenta', function ($attribute, $value, $parameters, $validator) {
            return Concepto::query()->sinCuenta()->find($value);
        });

        return [
            'id_concepto' => ['required', 'integer', 'exists:cadeco.conceptos,id_concepto,id_obra,' . Context::getIdObra(), 'sin_cuenta'],
            'cuenta' => ['required', "regex:'{$regex}'"]
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
