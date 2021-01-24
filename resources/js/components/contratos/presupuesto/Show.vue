<template>
    <span>
        <div class="card" v-if="!presupuesto">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <table id="tabla-conceptos" >
                            <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Clave</th>
                                    <th rowspan="2">Concepto</th>
                                    <th rowspan="2">UM</th>
                                    <th  class="contratado">Cantidad</th>
                                    <th class="destino"></th>
                                </tr>
                                <tr>
                                    <th  class="contratado">Solicitada</th>
                                    <th class="destino">Destino</th>
                                </tr>
                            </thead>
                            <tbody v-for="(concepto, i) in presupuesto.contrato_proyectado.contratos.data">
                                <tr v-if="concepto.unidad == null">
                                    <td >{{i + 1}}</td>
                                    <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        <b>{{concepto.descripcion}}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr v-else>
                                    <td >{{i + 1}}</td>
                                    <td :title="concepto.clave">{{ concepto.clave }}</td>
                                    <td :title="concepto.descripcion">
                                        <span v-for="n in concepto.nivel">-</span>
                                        {{concepto.descripcion}}
                                    </td>
                                    <td >{{concepto.unidad}}</td>
                                    <td class="numerico">{{concepto.cantidad_original_format}}</td>
                                    <td :title="concepto.destino.path" style="text-decoration: underline">{{ concepto.destino.destino_path }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                                <td>{{(partida.observaciones) ? partida.observaciones : '-------------------'}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
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
                                                    <td class="bg-gray-light" align="center" colspan="8"><b>{{(presupuesto.empresa) ? presupuesto.empresa.razon_social : '----- Proveedor Desconocido -----'}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{(presupuesto.sucursal) ? presupuesto.sucursal.descripcion : '------ Sin Sucursal ------'}}</td>
                                                    <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_usd_format}}</td>
                                                    <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_euro_format}}</td>
                                                    <td class="bg-gray-light"><b>ToTC LIBRA:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_libra_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light" colspan="3">{{(presupuesto.sucursal) ? presupuesto.sucursal.direccion : '------------------------------'}}</td>
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
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', null);
                return this.$store.dispatch('contratos/presupuesto/find', {
                    id: this.id,
                    params:{include: [
                        'contrato_proyectado.contratos.destino',
                        'contrato_proyectado.contratos.presupuesto:id_transaccion_presupuesto('+this.id+')',
                        'partidas.concepto',
                        'partidas.moneda',
                        'sucursal',
                        'empresa',]}
                }).then(data => {
                    this.$store.commit('contratos/presupuesto/SET_PRESUPUESTO', data);
                    this.cargando = false;
                });
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
table#tabla-conceptos {
    word-wrap: unset;
    width: 100%;
    background-color: white;
    border-color: transparent;
    border-collapse: collapse;
    clear: both;
}

table thead th
{
    padding: 0.2em;
    border: 1px solid #666;
    background-color: #333;
    color: white;
    font-weight: normal;
    overflow: hidden;
    text-align: center;
}

table thead th {
    text-align: center;
}
table tbody tr
{
    border-width: 0 1px 1px 1px;
    border-style: none solid solid solid;
    border-color: white #CCCCCC #CCCCCC #CCCCCC;
}
table tbody td,
table tbody th
{
    border-right: 1px solid #ccc;
    color: #242424;
    line-height: 20px;
    overflow: hidden;
    padding: 1px 5px;
    text-align: left;
    text-overflow: ellipsis;
    -o-text-overflow: ellipsis;
    -ms-text-overflow: ellipsis;
    white-space: nowrap;
}

table col.clave { width: 120px; }
table col.icon { width: 25px; }
table col.monto { width: 115px; }
table col.pct { width: 60px; }
table col.unidad { width: 80px; }
table col.clave  {width: 100px; }

table tbody td input.text
{
    border: none;
    padding: 0;
    margin: 0;
    width: 100%;
    background-color: transparent;
    font-family: inherit;
    font-size: inherit;
    font-weight: bold;
}

table tbody .numerico
{
    text-align: right;
    padding-left: 0;
    white-space: normal;
}

.text.is-invalid {
    color: #dc3545;
}

table tbody td input.text {
    text-align: right;
}
</style>
