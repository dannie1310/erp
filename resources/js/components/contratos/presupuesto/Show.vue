<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Ver Presupuesto">
            <i class="fa fa-eye" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> DETALLES DEL PRESUPUESTO CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="presupuesto">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <h5>Folio: &nbsp; <b>{{presupuesto.numero_folio}}</b></h5>
                                        </div>
                                    </div>
                                    <div class="table-responsive col-md-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="6"><b>{{(presupuesto.empresa) ? presupuesto.empresa.razon_social : '----- Proveedor Desconocido -----'}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{(presupuesto.sucursal) ? presupuesto.sucursal.descripcion : '------ Sin Sucursal ------'}}</td>
                                                    <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_usd_format}}</td>
                                                    <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_euro_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light">{{(presupuesto.sucursal) ? presupuesto.sucursal.direccion : '------------------------------'}}</td>
                                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.fecha_format}}</td>
                                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                                    <td class="bg-gray-light">{{'$ ' + (parseFloat(presupuesto.subtotal) + parseFloat(presupuesto.impuesto)).formatMoney(2,'.',',')}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th class="no_parte">Unidad</th>
                                                        <th class="no_parte">Cantidad Solicitada</th>
                                                        <th class="no_parte">Cantidad Aprobada</th>
                                                        <th>Precio Unitario</th>
                                                        <th>% Descuento</th>
                                                        <th>Precio Total</th>
                                                        <th>Moneda</th>
                                                        <th>Precio Total Moneda Conversión</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in presupuesto.partidas.data">
                                                        <td >{{i + 1}}</td>
                                                        <td style="text-align: left" v-if="partida.concepto" v-html="partida.concepto.descripcion_formato"></td>
                                                        <td v-else></td>
                                                        <td style="text-align: center">{{(partida.concepto) ? partida.concepto.unidad : '-----'}}</td>
                                                        <td style="text-align: center">{{(partida.concepto) ? partida.concepto.cantidad_original_format : '-----'}}</td>
                                                        <td style="text-align: center">{{(partida.concepto) ? partida.concepto.cantidad_presupuestada_format : '-----'}}</td>
                                                        <td class="money">{{partida.precio_unitario_format}}</td>
                                                        <td style="text-align: center">{{partida.descuento}}</td>
                                                        <td class="money">{{partida.precio_total}}</td>
                                                        <td style="text-align: center">{{(partida.moneda) ? partida.moneda.nombre : '------'}}</td>
                                                        <td class="money">{{partida.precio_total_moneda}}</td>
                                                        <td>{{partida.observaciones}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">% Descuento</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{presupuesto.descuento}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXP):</label>
                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{presupuesto.subtotal_format}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">IVA:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">{{presupuesto.impuesto_format}}</label>
                                    </div>
                                    <div class=" col-md-12" align="right">
                                        <label class="col-sm-2 col-form-label">Total:</label>
                                        <label class="col-sm-2 col-form-label money" style="text-align: right">{{total}}</label>
                                    </div>
                                    <hr>
                                    <div class="row col-md-12">
                                        <div class="col-md-2"><b>Anticipo:</b></div>
                                        <div class="col-md-2">{{presupuesto.anticipo}}</div>
                                        <div class="col-md-2"><b>Crédito (días):</b></div>
                                        <div class="col-md-2">{{presupuesto.dias_credito}}</div>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-2"><b>Vigencia( días):</b></div>
                                        <div class="col-md-2">{{presupuesto.dias_vigencia}}</div>
                                        <div class="col-md-2"><b>Observaciones:</b></div>
                                        <div class="col-md-6">{{presupuesto.observaciones}}</div>
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
        name: "presupuesto-show",
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        methods: {
            find() {

                this.cargando = true;                
                this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', null);
                return this.$store.dispatch('contratos/presupuesto/find', {
                    id: this.id,
                    params:{include: [
                        'contrato_proyectado',                        
                        'partidas.concepto',
                        'partidas.moneda',
                        'sucursal',
                        'empresa',]}
                }).then(data => {
                    this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;

                })
            }

        },
        computed: {
            presupuesto() {
                return this.$store.getters['contratos/presupuesto/currentPresupuesto'];
            },
            total()
            {
                return '$ ' + (parseFloat(this.presupuesto.subtotal) + parseFloat(this.presupuesto.impuesto)).formatMoney(2,'.',',');
            }
        }
    }
</script>

<style scoped>

</style>
