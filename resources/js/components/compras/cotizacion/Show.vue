<template>
    <span>
        <div  v-if="cargando">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                       <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <h5>Folio: &nbsp; <b>{{cotizacion.folio_format}}</b></h5>
                            </div>
                        </div>
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="bg-gray-light" align="center" colspan="6"><b>{{(cotizacion.empresa) ? cotizacion.empresa.razon_social : '----- Proveedor Desconocido -----'}}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Sucursal:</b></td>
                                        <td class="bg-gray-light">{{(cotizacion.sucursal) ? cotizacion.sucursal.descripcion : '------ Sin Sucursal ------'}}</td>
                                        <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                        <td class="bg-gray-light">{{(cotizacion.complemento) ? cotizacion.complemento.tc_usd_format : '---------------'}}</td>
                                        <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                        <td class="bg-gray-light">{{(cotizacion.complemento) ? cotizacion.complemento.tc_eur_format : '---------------'}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Direccion:</b></td>
                                        <td class="bg-gray-light">{{(cotizacion.sucursal) ? cotizacion.sucursal.direccion : '------------------------------'}}</td>
                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                        <td class="bg-gray-light">{{cotizacion.fecha_format}}</td>
                                        <td class="bg-gray-light"><b>Importe:</b></td>
                                        <td class="bg-gray-light">{{cotizacion.importe}}</td>
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
                                            <th class="no_parte">Núm de Parte</th>
                                            <th>Descripción</th>
                                            <th class="no_parte">Unidad</th>
                                            <th class="no_parte">Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>% Descuento</th>
                                            <th>Precio Total</th>
                                            <th>Moneda</th>
                                            <th>Precio Total Moneda Conversión</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(partida, i) in cotizacion.partidas.data" v-show="no_cotizados[i]">
                                            <td >{{cuenta[i] + 1}}</td>
                                            <td style="text-align: center"><b>{{(partida.material) ? partida.material.numero_parte : null}}</b></td>
                                            <td style="text-align: center">{{(partida.material) ? partida.material.descripcion : '------------'}}</td>
                                            <td style="text-align: center">{{(partida.material) ? partida.material.unidad : '-----'}}</td>
                                            <td style="text-align: center">{{partida.cantidad}}</td>
                                            <td class="money">{{partida.precio_unitario_format}}</td>
                                            <td style="text-align: center">{{partida.descuento}}</td>
                                            <td class="money">{{partida.precio_total}}</td>
                                            <td style="text-align: center">{{(partida.moneda) ? partida.moneda.nombre : '------'}}</td>
                                            <td class="money">{{partida.precio_total_moneda}}</td>
                                            <td>{{partida.observacion}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label">% Descuento</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{(cotizacion.complemento) ? cotizacion.complemento.descuento_format : '-----'}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXP):</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{cotizacion.subtotal}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label">IVA:</label>
                            <label class="col-sm-2 col-form-label money" style="text-align: right">{{cotizacion.impuesto}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label">Total:</label>
                            <label class="col-sm-2 col-form-label money" style="text-align: right">{{cotizacion.importe}}</label>
                        </div>
                        <div class="row col-md-12" v-if="cotizacion.complemento">
                            <div class="col-md-2"><b>Pago en Parcialidades (%):</b></div>
                            <div class="col-md-2">{{cotizacion.complemento.parcialidades_format}}</div>
                            <div class="col-md-2"><b>Anticipo:</b></div>
                            <div class="col-md-2">{{cotizacion.complemento.anticipo_format}}</div>
                        </div>
                        <div class="row col-md-12" v-if="cotizacion.complemento">
                            <div class="col-md-2"><b>Crédito (días):</b></div>
                            <div class="col-md-2">{{cotizacion.complemento.dias_credito}}</div>
                            <div class="col-md-2"><b>Tiempo de Entrega (días):</b></div>
                            <div class="col-md-2">{{cotizacion.complemento.entrega}}</div>
                        </div>
                        <div class="row col-md-12" v-if="cotizacion.complemento">
                            <div class="col-md-2"><b>Vigencia( días):</b></div>
                            <div class="col-md-2">{{cotizacion.complemento.vigencia}}</div>
                        </div>
                        <div class="row col-md-12">
                            <div class="col-md-2"><b>Observaciones:</b></div>
                            <div class="col-md-10">{{cotizacion.observaciones}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "cotizacion-show",
        props: ['id', 'show'],
        data(){
            return{
                cargando: false,
                no_cotizados: [],
                items: [],
                cuenta: [],
                x: 0,
                t: 0,
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                return this.$store.dispatch('compras/cotizacion/find', {
                    id: this.id,
                    params:{include: ['empresa', 'sucursal', 'complemento', 'partidas']}
                }).then(data => {
                    this.$store.commit('compras/cotizacion/SET_COTIZACION', data);
                    this.items = data.partidas.data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')


                })
                .finally(() => {
                    this.cargando = false;
                })
            }
        },
        computed: {
            cotizacion() {
                return this.$store.getters['compras/cotizacion/currentCotizacion']
            }
        },
        watch: {
            items()
            {
                this.x = 0;
                this.t = 0;
                while(this.x < this.items.length)
                {
                    this.no_cotizados[this.x] = this.items[this.x].no_cotizado;
                    this.cuenta[this.x] = (this.no_cotizados[this.x]) ? this.t ++ : 0;
                    this.x ++;
                }
            }
        }
    }
</script>

<style scoped>

</style>
