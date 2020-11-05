<template>
      <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar" v-if="$root.can('consultar_asignacion_contratista')">
             <i class="fa fa-eye"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE ASIGNACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row"  v-if="info">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Asignación: </h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio Asignación:</b></td>
                                                        <td class="bg-gray-light">{{info.numero_folio}}</td>
                                                        <td class="bg-gray-light"><b>Fecha Registro:</b></td>
                                                        <td class="bg-gray-light">{{info.fecha_format}}</td>
                                                        <td class="bg-gray-light"><b>Registro:</b></td>
                                                        <td class="bg-gray-light">{{info.usuario}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio Contrato:</b></td>
                                                        <td class="bg-gray-light">{{info.contrato.numero_folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{info.contrato.fecha}}</td>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{info.contrato.referencia}}</td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td class="bg-gray-light"><b>Contrato:</b></td>
                                                        <td class="bg-gray-light">{{subcontratos.contrato_folio_format}}</td>
                                                    </tr> -->

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class="col-12">
                                            <h5>Asignación Subcontrato: </h5>
                                        </div>
                                    </div>
                                    <div class="row" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light" align="center" colspan="6"><b>{{info.asignacionEstimacion.subcontrato.empresa}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.referencia}}</td>
                                                        <td class="bg-gray-light"><b>Subcontrato:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.numero_folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.fecha_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Tipo de Gasto:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.costo}}</td>
                                                        <td class="bg-gray-light"><b>Tipo de Contrato:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.tipo_subcontrato}}</td>
                                                        <td class="bg-gray-light"><b>Personalidad Contratista:</b></td>
                                                        <td class="bg-gray-light">{{info.asignacionEstimacion.subcontrato.personalidad_contratista}}</td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td class="bg-gray-light"><b>Contrato:</b></td>
                                                        <td class="bg-gray-light">{{subcontratos.contrato_folio_format}}</td>
                                                    </tr> -->

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                     <div class="row" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class="col-12">
                                            <h6><b>Detalle de las partidas</b></h6>
                                        </div>
                                    </div>
                                    <div class="row" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
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
                                                    <tr v-for="(doc, i) in info.asignacionEstimacion.subcontrato.partidas.data">
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
                                    <div class="row" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Subtotal Antes Descuento:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.subtotal_antes_descuento}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Descuento(%):</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.descuento}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Subtotal:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.subtotal_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">IVA:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.impuesto_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Retención IVA:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.impuesto_retenido}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Total:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{info.asignacionEstimacion.subcontrato.monto_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Moneda:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right"><i>{{info.asignacionEstimacion.subcontrato.moneda.nombre}}</i></label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Anticipo({{info.asignacionEstimacion.subcontrato.anticipo_format}}):</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right"><i>{{info.asignacionEstimacion.subcontrato.anticipo_monto_format}}</i></label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Fondo de Garantia:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right"><i>{{info.asignacionEstimacion.subcontrato.retencion}}</i></label>
                                         </div>
                                    </div>

                                    <hr>
                                    <div class="row col-md-12" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class="col-md-2"><b>Plazo de Ejecución:</b></div>
                                        <div class="col-md-4" v-if="info.asignacionEstimacion.subcontrato.subcontratos"><b>Del</b>&nbsp; {{info.asignacionEstimacion.subcontrato.subcontratos.fecha_ini_format}} &nbsp;<b>Al</b>&nbsp; {{info.asignacionEstimacion.subcontrato.subcontratos.fecha_fin_format}}</div>
                                        <div class="col-md-4" v-else><b>Del</b>&nbsp; -------- &nbsp;<b>Al</b>&nbsp; --------</div>

                                        <div class="col-md-2" v-if="info.asignacionEstimacion.subcontrato.subcontratos"><b>Descripción:</b></div>
                                        <div class="col-md-4" v-if="info.asignacionEstimacion.subcontrato.subcontratos">{{info.asignacionEstimacion.subcontrato.subcontratos.descripcion}}</div>
                                    </div>
                                    <br>
                                    <div class="row col-md-12" v-if="info.asignacionEstimacion && info.asignacionEstimacion.subcontrato">
                                        <div class="col-md-2"><b>Observaciones:</b></div>
                                        <div class="col-md-10">{{info.asignacionEstimacion.subcontrato.observaciones}}</div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-dark" align="left" style="width:19%;"><b>Plazo de Ejecución:</b></td>
                                                        <td align="left" v-if="subcontratos.subcontratos"><b>Del</b>&nbsp; {{subcontratos.subcontratos.fecha_ini}} &nbsp;<b>Al</b>&nbsp; {{subcontratos.subcontratos.fecha_fin}}</td>
                                                        <td align="left" v-else><b>Del</b>&nbsp; -------- &nbsp;<b>Al</b>&nbsp; --------</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Descripción:</b></td>
                                                        <td align="left" v-if="subcontratos.subcontratos">{{subcontratos.subcontratos.descripcion}}</td>
                                                        <td align="left" v-else></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>

</template>

<script>
    export default {
        name: "subcontrato-show",
        props: ['id'],
        data() {
            return {
                info:''
            }
        },
        methods: {
            find() {

                this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', null);
                return this.$store.dispatch('contratos/asignacion-contratista/find', {
                    id: this.id,
                    params: {include: ['asignacionEstimacion.subcontrato.partidas.contratos', 'asignacionEstimacion.subcontrato.moneda', 'asignacionEstimacion.subcontrato.subcontratos', 'contrato']}
                }).then(data => {
                    this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', data);
                    this.info = data;

                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>
