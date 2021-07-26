<template>
    <span>
        <div  v-if="invitacion === []">
            <div class="card">
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
        </div>
        <div v-else>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <datos-cotizacion-compra v-bind:cotizacion_compra="invitacion.cotizacionCompra"></datos-cotizacion-compra>
                                </div>
                            </div>
                            <div class="row" v-if="invitacion">
                                <div class="col-md-12">
                                    <div v-if="invitacion.cotizacionCompra">
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="6"><b>{{ invitacion.razon_social }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{ invitacion.descripcion_sucursal }}</td>
                                                    <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                                    <td class="bg-gray-light">{{(invitacion.cotizacionCompra.complemento) ? invitacion.cotizacionCompra.complemento.tc_usd_format : '---------------'}}</td>
                                                    <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                                    <td class="bg-gray-light">{{(invitacion.cotizacionCompra.complemento) ? invitacion.cotizacionCompra.complemento.tc_eur_format : '---------------'}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light">{{ invitacion.direccion_sucursal }}</td>
                                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                                    <td class="bg-gray-light">{{ invitacion.cotizacionCompra.fecha_format}}</td>
                                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                                    <td class="bg-gray-light">{{invitacion.importe_cotizacion}}</td>
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
                                                        <tr v-for="(partida, i) in invitacion.cotizacionCompra.partidas.data" v-if="partida.no_cotizado">
                                                            <td >{{i + 1}}</td>
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
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{(invitacion.cotizacionCompra.complemento) ? invitacion.cotizacionCompra.complemento.descuento_format : '-----'}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                            <label class="col-sm-2 col-form-label" style="text-align: right">{{invitacion.cotizacionCompra.subtotal}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label">IVA:</label>
                                            <label class="col-sm-2 col-form-label money" style="text-align: right">{{invitacion.cotizacionCompra.impuesto}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-sm-2 col-form-label">Total:</label>
                                            <label class="col-sm-2 col-form-label money" style="text-align: right">{{invitacion.cotizacionCompra.importe}}</label>
                                        </div>
                                        <div class="row col-md-12" v-if="invitacion.cotizacionCompra.complemento">
                                            <div class="col-md-2"><b>Pago en Parcialidades (%):</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.parcialidades_format}}</div>
                                            <div class="col-md-2"><b>Anticipo:</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.anticipo_format}}</div>
                                        </div>
                                        <div class="row col-md-12" v-if="invitacion.cotizacionCompra.complemento">
                                            <div class="col-md-2"><b>Crédito (días):</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.dias_credito}}</div>
                                            <div class="col-md-2"><b>Tiempo de Entrega (días):</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.entrega}}</div>
                                        </div>
                                        <div class="row col-md-12" v-if="invitacion.cotizacionCompra.complemento">
                                            <div class="col-md-2"><b>Vigencia( días):</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.vigencia}}</div>
                                        </div>
                                        <div class="row col-md-12">
                                            <div class="col-md-2"><b>Observaciones:</b></div>
                                            <div class="col-md-10">{{invitacion.cotizacionCompra.observaciones}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosCotizacionCompra from "./partials/DatosCotizacionCompra";
    export default {
        name: "cotizacion-proveedor-show",
        components: {DatosCotizacionCompra},
        props: ['id'],
        data(){
            return{
                cargando: false,
                invitacion: []
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id,
                    params:{ include: ['cotizacionCompra.complemento','cotizacionCompra.empresa','cotizacionCompra.sucursal','cotizacionCompra.partidas'], scope: ['invitadoAutenticado']}
                }).then(data => {
                    this.invitacion = data;
                })
                .finally(() => {
                    this.cargando = false;
                })
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
