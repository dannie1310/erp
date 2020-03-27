<template>
      <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar" v-if="$root.can('consultar_subcontrato')">
             <i class="fa fa-eye"></i>
         </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> CONSULTA DE SUBCONTRATO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row"  v-if="subcontratos">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Subcontrato: <b>{{subcontratos.numero_folio_format}}</b></h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Referencia:</b></td>
                                                        <td align="left">{{subcontratos.referencia}}</td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left"><b>Folio:</b></td>
                                                        <td class="bg-dark" align="right">{{subcontratos.numero_folio_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left"><b>Fecha:</b></td>
                                                        <td class="bg-dark" align="right">{{subcontratos.fecha_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"></td>
                                                        <td class="bg-dark" align="left" style="width:10%;"><b>Contrato:</b></td>
                                                        <td class="bg-dark" align="right" style="width:10%;">{{subcontratos.contrato_folio_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-dark" align="center" colspan="5"><h4><b>{{subcontratos.empresa}}</b></h4></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-12">
                                            <h6><b>Detalle de las partidas</b></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="bg-dark">#</th>
                                                        <th class="bg-dark">Clave</th>
                                                        <th class="bg-dark">Descripción</th>
                                                        <th class="bg-dark">Unidad</th>
                                                        <th class="bg-dark">Cantidad</th>
                                                        <th class="bg-dark">Precio</th>
                                                        <th class="bg-dark">Importe</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(doc, i) in subcontratos.partidas.data">
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
                                    <div class="row">
                                        <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Subtotal Antes Descuento:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_antes_descuento}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Descuento(%):</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.descuento}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Subtotal:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">IVA:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.impuesto_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Retención IVA:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.impuesto_retenido}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Total:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.monto_format}}</label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Moneda:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.moneda.nombre}}</i></label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Anticipo({{subcontratos.anticipo_format}}):</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.anticipo_monto_format}}</i></label>
                                         </div>
                                         <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label" style="text-align: left">Fondo de Garantia:</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.retencion}}</i></label>
                                         </div>
                                    </div>
                                    <div class="row">
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
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Tipo de Gasto:</b></td>
                                                        <td align="left">{{subcontratos.costo}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Tipo de Contrato:</b></td>
                                                        <td align="left">{{subcontratos.tipo_subcontrato}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-dark" align="left"><b>Personalidad Contratista:</b></td>
                                                        <td align="left">{{subcontratos.personalidad_contratista}}</td>
                                                    </tr>                                                  
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
        props: ['id' , 'show'],
        data() {
            return {
                subcontratos: ''
            }
        },
        methods: {
            find() {

                this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: {include: ['partidas', 'moneda', 'partidas.contratos', 'subcontratos']}
                }).then(data => {
                    this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', data);
                    this.subcontratos = data;
                    
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>
