<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="visualizar">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header" v-if="venta">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-paper-plane"></i>&nbsp; VENTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="cargando">
                         <div>
                             <h5  id="exampleModalLongTitle"><i class="fa fa-spin fa-spinner"></i>CARGANDO</h5>
                         </div>
                    </div>
                    <div class="modal-body" v-else>
                        <div class="row" v-if="venta">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <b>Datos de la Venta</b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Folio:</b></td>
                                                        <td class="bg-gray-light">{{venta.folio_format}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{venta.fecha_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Empresa:</b></td>
                                                        <td class="bg-gray-light">{{venta.empresa.razon_social}}</td>
                                                        <td class="bg-gray-light"><b>Monto:</b></td>
                                                        <td class="bg-gray-light">{{venta.monto}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>RFC:</b></td>
                                                        <td class="bg-gray-light">{{venta.empresa.rfc}}</td>
                                                        <td class="bg-gray-light"><b>Estado:</b></td>
                                                        <td class="bg-gray-light">
                                                            <small class="badge" :class="{'badge-danger': venta.estado.id == '-1',
                                                                                         'badge-primary': venta.estado.id == '0',
                                                                                         'badge-success': venta.estado.id == '1'}">
                                                                 {{ venta.estado.descripcion }} </small></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Observaciones:</b></td>
                                                        <td class="bg-gray-light">{{venta.observaciones_format}}</td>
                                                        <td class="bg-gray-light" v-if="venta.usuario"><b>Usuario Registró</b></td>
                                                        <td class="bg-gray-light" v-else="venta.usuario"></td>
                                                        <td class="bg-gray-light" v-if="venta.usuario">{{venta.usuario.nombre}}</td>
                                                        <td class="bg-gray-light" v-else="venta.usuario"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-12">
                                           <b>Detalle de las partidas</b>
                                        </div>
                                     </div>
                                    <div class="row">
                                        <div class="table-responsive col-md-12">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>No. de Parte</th>
                                                        <th>Descripción</th>
                                                        <th>Unidad</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio/U</th>
                                                        <th>Importe</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(partida, i) in venta.partidas.data">
                                                        <td>{{i+1}}</td>
                                                        <td >{{partida.material.numero_parte}}</td>
                                                        <td >{{partida.material.descripcion}}</td>
                                                        <td>{{partida.unidad}}</td>
                                                        <td style="text-align: right">{{partida.cantidad_decimal}}</td>
                                                        <td style="text-align: right">{{partida.precio_unitario}}</td>
                                                        <td style="text-align: right">{{partida.importe}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">Subtotal:</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.subtotal}}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">IVA(16%)</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.impuesto}}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class=" col-md-12" align="right">
                                                        <label class="col-sm-2 col-form-label"></label>
                                                        <label class="col-sm-2 col-form-label">Total:</label>
                                                        <label class="col-sm-2 col-form-label" style="text-align: right">{{venta.monto}}</label>
                                                </div>
                                            </div>
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
        name: "venta-show",
        props: ['id','pagina'],
        data() {
            return {
                data: [],
                motivo: '',
                indice: '',
                cargo: '',
                cargando: false,
            }
        },
        methods: {
            find() {
                $(this.$refs.modal).modal('show');
                this.cargando = true;
                this.motivo = '';
                this.$store.commit('ventas/venta/SET_VENTA', null);
                return this.$store.dispatch('ventas/venta/find', {
                    id: this.id,
                    params: {include: ['empresa', 'partidas.material', 'usuario', 'estado']}
                }).then(data => {
                    this.$store.commit('ventas/venta/SET_VENTA', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
        },
        computed: {
            venta() {
                // console.log('Salida','jorge ventaaaaa');
                return this.$store.getters['ventas/venta/currentVenta'];
            }
        }
    }
</script>
