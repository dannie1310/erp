<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Costo;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class StoreEstimacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_estimacion_subcontrato');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_antecedente' => ['required', 'integer'],
            'fecha' => ['required', 'date_format:"Y-m-d"'],
            'cumplimiento' => ['required', 'date_format:"Y-m-d"'],
            'vencimiento' => ['required', 'date_format:"Y-m-d"', 'after_or_equal:cumplimiento'],
            'observaciones' => ['string'],
            'conceptos' => ['required', 'array'],
            'conceptos.*.item_antecedente' => ['required', 'integer'],
            'conceptos.*.id_concepto' => ['required', 'integer'],
            'conceptos.*.importe' => ['required', 'numeric'],
            'conceptos.*.cantidad' => ['required', 'numeric'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}