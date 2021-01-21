<template>
    <div class="row" v-if="solicitud_cambio">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Información</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Folio:</label>
                                <div class="col-md-8">
                                    {{solicitud_cambio.numero_folio_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Fecha:</label>
                                <div class="col-md-8">
                                    {{solicitud_cambio.fecha_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Monto:</label>
                                <div class="col-md-8">
                                    {{solicitud_cambio.monto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">IVA:</label>
                                <div class="col-md-8">
                                    {{solicitud_cambio.impuesto_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Moneda:</label>
                                <div class="col-md-8">
                                    {{solicitud_cambio.moneda.nombre}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-md-6 col-form-label">Porcentaje:</label>
                                <div class="col-md-6">
                                    {{ (Number(solicitud_cambio.monto) / Number(solicitud_cambio.subcontrato.monto) * 100).formatMoney(4) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Registro:</label>
                                <div class="col-md-8">
                                    {{ solicitud_cambio.usuario_registro }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="col-md-6 col-form-label">Fecha y Hora de Registro:</label>
                                <div class="col-md-6">
                                    {{ solicitud_cambio.fecha_hora_registro_format }}
                                </div>
                            </div>
                        </div>

                        <template v-if="solicitud_cambio.complemento">
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" v-if="solicitud_cambio.complemento.tipo==1">Canceló:</label>
                                    <label class="col-md-4 col-form-label" v-else-if="solicitud_cambio.complemento.tipo==2">Aplicó:</label>
                                    <label class="col-md-4 col-form-label" v-else-if="solicitud_cambio.complemento.tipo==3">Rechazó:</label>
                                    <div class="col-md-8">
                                        {{ solicitud_cambio.complemento.usuario.nombre }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row">
                                    <label class="col-md-6 col-form-label">Fecha y Hora:</label>
                                    <div class="col-md-6">
                                        {{ solicitud_cambio.complemento.fecha_hora_format }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="row" v-if="solicitud_cambio.complemento">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" v-if="solicitud_cambio.complemento.tipo==1">Motivo de Cancelación:</label>
                                <label class="col-md-2 col-form-label" v-else-if="solicitud_cambio.complemento.tipo==3">Motivo de Rechazo:</label>
                                <div class="col-md-10">
                                    {{ solicitud_cambio.complemento.motivo }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group pull-right">
                        <router-link  :to="{ name: 'solicitud-cambio-show', params: {id: solicitud_cambio.id}}" v-if="$root.can('consultar_solicitud_cambio_subcontrato')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                            <i class="fa fa-eye"></i>
                        </router-link>
                        <PDF v-bind:id="solicitud_cambio.id" v-if="$root.can('consultar_solicitud_cambio_subcontrato')"></PDF>
                        <router-link  :to="{ name: 'solicitud-cambio-documentos', params: {id: solicitud_cambio.id}}" v-if="$root.can('consultar_solicitud_cambio_subcontrato') && $root.can('consultar_archivos_transaccion')" target="_blank" type="button" class="btn btn-sm btn-outline-primary pull-right" title="Ver Documentos">
                            <i class="fa fa-folder-open"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PDF from '../Formato';
export default {
    name: "DatosSolicitud",
    components: {PDF},
    props: ['solicitud_cambio'],
}
</script>

<style scoped>

</style>
