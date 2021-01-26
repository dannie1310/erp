<template>
    <div class="row" v-if="presupuesto">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Información de Presupuesto de Contratista</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Folio:</label>
                                <div class="col-md-8">
                                    {{presupuesto.numero_folio_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Fecha:</label>
                                <div class="col-md-8">
                                    {{presupuesto.fecha_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Contratista:</label>
                                <div class="col-md-10">
                                    {{presupuesto.empresa.razon_social}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Sucursal:</label>
                                <div class="col-md-8">
                                    {{presupuesto.sucursal.descripcion}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Impuesto:</label>
                                <div class="col-md-8">
                                    {{presupuesto.impuesto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Monto:</label>
                                <div class="col-md-8">
                                    {{presupuesto.monto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Registro:</label>
                                <div class="col-md-8">
                                    {{ presupuesto.usuario_registro }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-6 col-form-label">Fecha y Hora de Registro:</label>
                                <div class="col-md-6">
                                    {{ presupuesto.fecha_hora_registro_format }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">% Anticipo:</label>
                                <div class="col-md-8">
                                    {{presupuesto.porcentaje_anticipo_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Crédito (días):</label>
                                <div class="col-md-8">
                                    {{presupuesto.dias_credito}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Vigencia (días):</label>
                                <div class="col-md-8">
                                    {{ presupuesto.dias_vigencia }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Observaciones:</label>
                                <div class="col-md-10">
                                    {{ presupuesto.observaciones }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <PresupuestoButtons class="pull-right" :value="presupuesto"></PresupuestoButtons>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PDF from '../FormatoTablaComparativa';
import EstatusLabelGlobal from "../../../globals/EstatusLabel";
import ActionButtons from "../../../compras/orden-compra/partials/ActionButtons";
import PresupuestoButtons from "./ActionButtons";
export default {
    name: "DatosPresupuesto",
    components: {PresupuestoButtons, ActionButtons, EstatusLabelGlobal, PDF},
    props: ['presupuesto'],
}
</script>

<style scoped>

</style>
