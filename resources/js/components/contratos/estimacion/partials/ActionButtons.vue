<template>
    <span>
        <div class="btn-group">
            <button title="Aprobar" @click="resumen('aprobar')" v-if="value.aprobar" type="button"
                    class="btn btn-sm btn-outline-success" :disabled="aprobando">
                <i v-if="aprobando" class="fa fa-spin fa-spinner"></i>
                <i v-else class="fa fa-thumbs-o-up"></i>
            </button>
            <button title="Revertir Aprobación" @click="resumen('revertir')" v-if="value.desaprobar" type="button"
                    class="btn btn-sm btn-outline-danger" :disabled="revirtiendo">
                <i v-if="revirtiendo" class="fa fa-spin fa-spinner"></i>
                <i v-else class="fa fa-thumbs-down"></i>
            </button>
            <router-link  :to="{ name: 'estimacion-show', params: {id: value.id}}" v-if="$root.can('consultar_estimacion_subcontrato')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
                <i class="fa fa-eye"></i>
            </router-link>
            <EditDL v-bind:value="value" v-if="value.edit && (value.estado == 0)" />
            <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" v-if="value.delete && (value.estado == 0)"  v-bind:id="value.id">
                <i class="fa fa-trash"></i>
            </button>
            <PDF v-bind:id="value.id"></PDF>
            <Relaciones v-bind:transaccion="value.transaccion"/>
            <router-link  :to="{ name: 'estimacion-documentos', params: {id: value.id}}" v-if="$root.can('consultar_estimacion_subcontrato') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Documentos">
                <i class="fa fa-folder-open"></i>
            </router-link>
        </div>


        <!-- Modal -->
        <div class="modal fade" ref="resumen" tabindex="-1" role="dialog" aria-labelledby="resumenLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resumenLabel">Resumen de Estimación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table style="width: 100%" class="table table-stripped">
                                <tbody>
                                <tr>
                                    <th style="text-align: left" colspan="2">Importe Estimación</th>
                                    <td style="text-align: right">{{value.estimacion.suma_importes}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left">Amortización de Anticipo</th>
                                    <td>{{value.estimacion.anticipo}}</td>
                                    <td style="text-align: right">{{ value.estimacion.monto_anticipo_aplicado_format }}</td>
                                </tr>
                                <tr v-if="configuracion.ret_fon_gar_antes_iva == 1">
                                    <th style="text-align: left">Retención de Fondo de Garantia</th>
                                    <td>{{value.estimacion.retencion}} %</td>
                                    <td style="text-align: right">{{value.estimacion.retencion_fondo_garantia}}</td>
                                </tr>
                                <tr v-if="configuracion.retenciones_antes_iva == 1">
                                    <th style="text-align: left" colspan="2">Total Retenciones</th>
                                    <td style="text-align: right">{{value.estimacion.total_retenciones}}</td>
                                </tr>
                                <tr v-if="configuracion.retenciones_antes_iva == 1">
                                    <th style="text-align: left" colspan="2">Total Retenciones Liberadas</th>
                                    <td style="text-align: right">{{value.estimacion.total_retencion_liberadas}}</td>
                                </tr>
                                <tr v-if="configuracion.desc_pres_mat_antes_iva == 1">
                                    <th style="text-align: left" colspan="2">Total Deductivas</th>
                                    <td style="text-align: right">{{value.estimacion.total_deductivas}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Subtotal</th>
                                    <td style="text-align: right">{{value.estimacion.subtotal_orden_pago}}</td>
                                </tr>
                                 <tr>
                                     <th style="text-align: left">IVA</th>
                                     <td>{{ value.estimacion.tasa_iva_format }} %</td>
                                     <td style="text-align: right">{{value.estimacion.iva_orden_pago}}</td>
                                 </tr>
                                <tr>
                                    <th style="text-align: left">Retención de IVA</th>
                                    <td>{{value.estimacion.retencion_iva_porcentaje}}</td>
                                    <td style="text-align: right">{{value.estimacion.retencion_iva_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left">Retención de ISR</th>
                                    <td>{{value.estimacion.porcentaje_isr_retenido}} %</td>
                                    <td style="text-align: right">{{value.estimacion.monto_isr_retenido_format}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Total</th>
                                    <td style="text-align: right">{{value.estimacion.total_orden_pago}}</td>
                                </tr>
                                <tr v-if="configuracion.ret_fon_gar_antes_iva == 0">
                                    <th style="text-align: left">Retención de Fondo de Garantia Estimación</th>
                                    <td v-if="configuracion.ret_fon_gar_con_iva == 1">{{value.estimacion.retencion}} % + IVA</td>
                                    <td v-else>{{value.estimacion.retencion}} %</td>
                                    <td style="text-align: right">{{value.estimacion.retencion_fondo_garantia}}</td>
                                </tr>
                                <tr v-if="configuracion.desc_pres_mat_antes_iva == 0">
                                    <th style="text-align: left" colspan="2">Total Deductivas</th>
                                    <td style="text-align: right">{{value.estimacion.total_deductivas}}</td>
                                </tr>
                                   <tr v-if="configuracion.retenciones_antes_iva == 0">
                                    <th style="text-align: left" colspan="2">Total Retenciones</th>
                                    <td style="text-align: right">{{value.estimacion.total_retenciones}}</td>
                                </tr>
                                <tr v-if="configuracion.retenciones_antes_iva == 0">
                                    <th style="text-align: left" colspan="2">Total Retenciones Liberadas</th>
                                    <td style="text-align: right">{{value.estimacion.total_retencion_liberadas}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Total Anticipo a Liberar</th>
                                    <td style="text-align: right">{{value.estimacion.total_anticipo_liberar}}</td>
                                </tr>
                                <tr>
                                    <th style="text-align: left" colspan="2">Total a Pagar</th>
                                    <td style="text-align: right">{{ value.estimacion.monto_pagar_format }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button @click="aprobando ? aprobar() : desaprobar()" type="button" class="btn btn-primary" :disabled="guardando">
                            <span v-if="guardando">
                                <i class="fa fa-spin fa-spinner"></i>
                            </span>
                            <span v-else>
                                {{ aprobando ? 'Aprobar' : 'Revertir Aprobación' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoEstimacion';
    import Relaciones from "../../../globals/ModalRelaciones";
    import EditDL from './EditDropList';
    export default {
        name: "action-buttons",
        components: {PDF, Relaciones, EditDL},
        props: ['value'],
        data() {
            return {
                aprobando: false,
                revirtiendo: false,
                guardando: false,
                configuracion : []
            }
        },

        mounted() {
            $(this.$refs.resumen).on('hidden.bs.modal', () => {
                this.aprobando = false;
                this.revirtiendo = false;
            })
        },

        methods: {
            resumen(opcion) {
                if (opcion == 'aprobar') {this.aprobando = true;}
                else {this.revirtiendo = true;}
                $(this.$refs.resumen).appendTo('body')
                $(this.$refs.resumen).modal('show');
                this.getConfiguraciones()
            },

            aprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/estimacion/aprobar' ,{ id: this.value.id })
                    .then(() => {
                        this.$store.commit('contratos/estimacion/APROBAR_ESTIMACION', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },

            desaprobar() {
                this.guardando = true;
                return this.$store.dispatch('contratos/estimacion/revertirAprobacion', {id: this.value.id})
                    .then(() => {
                        this.$store.commit('contratos/estimacion/REVERTIR_APROBACION', this.value.id);
                    })
                    .finally(() => {
                        this.guardando = false;
                        $(this.$refs.resumen).modal('hide');
                    })
            },
            show(){
                this.$router.push({ name:'estimacion-show', params: {id: this.value.id}});
            },
            edit(){
                this.$router.push({ name:'estimacion-edit', params: {id: this.value.id}});
            },
            eliminar() {
                this.$router.push({name: 'estimacion-delete', params: {id: this.value.id}});
            },
            getConfiguraciones() {
                this.cargando = true;
                return this.$store.dispatch('finanzas/estimacion/index', { params: this.query1 } )
                    .then(data => {
                       this.configuracion = data.data[0]
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        }
    }
</script>
