<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-danger" :disabled="cargando" title="Eliminar Cotización">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> DETALLES DE LA COTIZACIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="invitacion">
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row col-md-12">
                                            <div class="col-md-6">
                                                <h5>Folio: &nbsp; <b>{{invitacion.cotizacionCompra.folio_format}}</b></h5>
                                            </div>
                                        </div>
                                        <div class="table-responsive col-md-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td class="bg-gray-light" align="center" colspan="8"><b>{{invitacion.razon_social}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.descripcion_sucursal}}</td>
                                                        <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.cotizacionCompra.complemento.tc_usd_format}}</td>
                                                        <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.cotizacionCompra.complemento.tc_eur_format}}</td>
                                                        <td class="bg-gray-light"><b>ToTC LIBRA:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.cotizacionCompra.complemento.tc_libra_format}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-gray-light"><b>Direccion:</b></td>
                                                        <td class="bg-gray-light" colspan="3">{{invitacion.direccion_sucursal}}</td>
                                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.cotizacionCompra.fecha_format}}</td>
                                                        <td class="bg-gray-light"><b>Importe:</b></td>
                                                        <td class="bg-gray-light">{{invitacion.cotizacionCompra.importe}}</td>
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
                                                            <th class="no_parte">No. de Parte</th>
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
                                                        <tr v-for="(partida, i) in invitacion.cotizacionCompra.partidas.data">
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
                                            <label class="col-md-2 col-form-label">% Descuento</label>
                                            <label class="col-md-2 col-form-label" style="text-align: right">{{invitacion.cotizacionCompra.complemento.descuento_format}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                            <label class="col-md-2 col-form-label" style="text-align: right">{{invitacion.cotizacionCompra.subtotal}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-2 col-form-label">IVA:</label>
                                            <label class="col-md-2 col-form-label money" style="text-align: right">{{invitacion.cotizacionCompra.impuesto}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-2 col-form-label">Total:</label>
                                            <label class="col-md-2 col-form-label money" style="text-align: right">{{invitacion.cotizacionCompra.importe}}</label>
                                        </div>
                                        <div class="row col-md-12" v-if="invitacion.cotizacionCompra.complemento">
                                            <div class="col-md-2"><b>Pago en Parcialidades (%):</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.parcialidades}}</div>
                                            <div class="col-md-2"><b>Anticipo:</b></div>
                                            <div class="col-md-2">{{invitacion.cotizacionCompra.complemento.anticipo}}</div>
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
                                    <hr />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
                                                <div class="col-md-10">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times-circle"></i>
                                Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''">
                                <i class="fa fa-trash"></i>
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "cotizacion-proveedor-delete",
        props: ['id_invitacion'],
        data(){
            return{
                cargando : false,
                motivo : '',
                invitacion : null
            }
        },
        methods: {
            find() {
                this.motivo = ''
                this.cargando = true;
                return this.$store.dispatch('padronProveedores/invitacion/find', {
                    id: this.id_invitacion,
                    params:{ include: ['cotizacionCompra.complemento','cotizacionCompra.empresa','cotizacionCompra.sucursal','cotizacionCompra.partidas'], scope: ['invitadoAutenticado']}
                }).then(data => {
                    this.invitacion = data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/cotizacion/eliminarProveedor', {
                    id: this.id_invitacion,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        $(this.$refs.modal).modal('hide');
                        this.cargando = true;
                        return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                            params: {include: ['transaccion','cotizacion'], scope: ['cotizacionRealizada','invitadoAutenticado']},

                    })
                        .then(data => {
                            this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                            this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
                        })
                    })
                    .finally( ()=>{
                        this.cargando = false;
                    });
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
        }
    }
</script>

<style scoped>

</style>
