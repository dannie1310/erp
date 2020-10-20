<template>
    <span>
        <div class="row" v-if="!asignacion">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="card" v-if="asignacion">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light"><b>Folio Asignación:</b></td>
                                <td class="bg-gray-light">{{asignacion.numero_folio}}</td>
                                <td class="bg-gray-light"><b>Fecha Registro:</b></td>
                                <td class="bg-gray-light">{{asignacion.fecha_format}}</td>
                                <td class="bg-gray-light"><b>Registro:</b></td>
                                <td class="bg-gray-light">{{asignacion.usuario}}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray-light"><b>Folio Contrato:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.numero_folio_format}}</td>
                                <td class="bg-gray-light"><b>Fecha:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.fecha}}</td>
                                <td class="bg-gray-light"><b>Referencia:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.referencia}}</td>
                            </tr>
                            <!-- <tr>
                                <td class="bg-gray-light"><b>Contrato:</b></td>
                                <td class="bg-gray-light">{{subcontratos.contrato_folio_format}}</td>
                            </tr> -->

                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Asignación Subcontrato: </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="bg-gray-light" align="center" colspan="6"><b>{{asignacion.asignacionEstimacion.subcontrato.empresa}}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.referencia}}</td>
                                        <td class="bg-gray-light"><b>Subcontrato:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.numero_folio_format}}</td>
                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.fecha_format}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Tipo de Gasto:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.costo}}</td>
                                        <td class="bg-gray-light"><b>Tipo de Contrato:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.tipo_subcontrato}}</td>
                                        <td class="bg-gray-light"><b>Personalidad Contratista:</b></td>
                                        <td class="bg-gray-light">{{asignacion.asignacionEstimacion.subcontrato.personalidad_contratista}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-header">
                        <h6><b>Detalle de las partidas</b></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Clave</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(doc, i) in asignacion.asignacionEstimacion.subcontrato.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td class="td_numero_folio"></td>
                                        <td>{{doc.contratos.descripcion}}</td>
                                        <td align="center">{{doc.contratos.unidad}}</td>
                                        <td class="td_money">{{doc.cantidad_format}}</td>
                                        <td class="td_money">{{doc.precio_unitario_format}}</td>
                                        <td class="td_money">{{doc.importe_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Subtotal Antes Descuento:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.subtotal_antes_descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Descuento(%):</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Subtotal:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.subtotal_format}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">IVA:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.impuesto_format}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Retención IVA:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.impuesto_retenido}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Total:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right">{{asignacion.asignacionEstimacion.subcontrato.monto_format}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Moneda:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right"><i>{{asignacion.asignacionEstimacion.subcontrato.moneda.nombre}}</i></label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Anticipo({{asignacion.asignacionEstimacion.subcontrato.anticipo_format}}):</label>
                            <label class="col-md-2 col-form-label" style="text-align: right"><i>{{asignacion.asignacionEstimacion.subcontrato.anticipo_monto_format}}</i></label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-md-2 col-form-label" style="text-align: left">Fondo de Garantia:</label>
                            <label class="col-md-2 col-form-label" style="text-align: right"><i>{{asignacion.asignacionEstimacion.subcontrato.retencion}}</i></label>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="table-responsive col-md-12">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="bg-white"><b>Plazo de Ejecución:</b></td>
                                    <td class="bg-white" v-if="asignacion.asignacionEstimacion.subcontrato.subcontratos"><b>Del</b>&nbsp; {{asignacion.asignacionEstimacion.subcontrato.subcontratos.fecha_ini}} &nbsp;<b>Al</b>&nbsp; {{asignacion.asignacionEstimacion.subcontrato.subcontratos.fecha_fin}}</td>
                                    <td class="bg-white" v-else><b>Del</b>&nbsp; -------- &nbsp;<b>Al</b>&nbsp; --------</td>
                                    <td class="bg-white"><b>Descripción:</b></td>
                                    <td class="bg-white">{{asignacion.asignacionEstimacion.subcontrato.subcontratos.descripcion}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-white"><b>Observaciones:</b></td>
                                    <td class="bg-white" colspan="3">{{asignacion.asignacionEstimacion.subcontrato.observaciones}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Motivo: </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row error-content">
                            <div class="col-md-12">
                                <textarea
                                    name="motivo"
                                    id="motivo"
                                    class="form-control"
                                    v-model="motivo"
                                    v-validate="{required: true}"
                                    data-vv-as="Motivo"
                                    :class="{'is-invalid': errors.has('motivo')}"
                                ></textarea>
                                <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                        <button type="button" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''" v-on:click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "asignacion-proveedor-delete",
        props: ['id'],
        data() {
            return {
                motivo: ''
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', null);
                return this.$store.dispatch('contratos/asignacion-contratista/find', {
                    id: this.id,
                    params: {include: ['asignacionEstimacion.subcontrato.partidas.contratos', 'asignacionEstimacion.subcontrato.moneda', 'asignacionEstimacion.subcontrato.subcontratos', 'contrato']}
                }).then(data => {
                    this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', data);
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
            eliminar() {
                return this.$store.dispatch('contratos/asignacion-contratista/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.$router.push({name: 'estimacion'});
                    })
            },
            salir(){
                this.$router.push({name: 'estimacion'});
            },
        },
        computed: {
            asignacion() {
                return this.$store.getters['contratos/asignacion-contratista/currentAsignacion'];
            }
        },
    }
</script>

<style scoped>

</style>
