<template>
    <div class="row" v-if="contrato_proyectado">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Información de Contrato Proyectado</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-6 col-form-label">Área Subcontratante:</label>
                                <div class="col-md-6">
                                    {{contrato_proyectado.area_subcontratante}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Folio:</label>
                                <div class="col-md-8">
                                    {{contrato_proyectado.numero_folio_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Referencia:</label>
                                <div class="col-md-8">
                                    {{contrato_proyectado.referencia}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Fecha:</label>
                                <div class="col-md-8">
                                    {{contrato_proyectado.fecha_format}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Registro:</label>
                                <div class="col-md-8">
                                    {{ contrato_proyectado.usuario_registro }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-md-6 col-form-label">Fecha y Hora de Registro:</label>
                                <div class="col-md-6">
                                    {{ contrato_proyectado.fecha_hora_registro_format }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label">Observaciones:</label>
                                <div class="col-md-10">
                                    {{ contrato_proyectado.observaciones }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <ButtonsContratoProyectado class="pull-right" :value="contrato_proyectado"></ButtonsContratoProyectado>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ButtonsContratoProyectado from "./ActionButtons";
export default {
    name: "DatosContratoProyectado",
    components: {ButtonsContratoProyectado},
    props: ['contrato_proyectado'],
}
</script>

<style scoped>

</style>
