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
                        <div class="modal-body" v-if="cotizacion">
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
                                                        <tr v-for="(partida, i) in cotizacion.partidas.data">
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
                                            <label class="col-md-2 col-form-label" style="text-align: right">{{(cotizacion.complemento) ? cotizacion.complemento.descuento_format : '-----'}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
                                            <label class="col-md-2 col-form-label" style="text-align: right">{{cotizacion.subtotal}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-2 col-form-label">IVA ({{cotizacion.tasa_iva_format}}%):</label>
                                            <label class="col-md-2 col-form-label money" style="text-align: right">{{cotizacion.impuesto}}</label>
                                        </div>
                                        <div class=" col-md-12" align="right">
                                            <label class="col-md-2 col-form-label">Total:</label>
                                            <label class="col-md-2 col-form-label money" style="text-align: right">{{cotizacion.importe}}</label>
                                        </div>
                                        <div class="row col-md-12" v-if="cotizacion.complemento">
                                            <div class="col-md-2"><b>Pago en Parcialidades (%):</b></div>
                                            <div class="col-md-2">{{cotizacion.complemento.parcialidades}}</div>
                                            <div class="col-md-2"><b>Anticipo:</b></div>
                                            <div class="col-md-2">{{cotizacion.complemento.anticipo}}</div>
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
        name: "cotizacion-delete",
        props: ['id'],
        data(){
            return{
                cargando : false,
                motivo : ''
            }
        },
        methods: {
            find() {
                this.motivo = ''
                this.cargando = true;
                this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                return this.$store.dispatch('compras/cotizacion/find', {
                    id: this.id,
                    params:{include: ['empresa', 'sucursal', 'complemento', 'partidas']}
                }).then(data => {
                    this.$store.commit('compras/cotizacion/SET_COTIZACION', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;
                })
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/cotizacion/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.$store.commit('compras/cotizacion/DELETE_COTIZACION', {id: this.id})
                        $(this.$refs.modal).modal('hide');
                        this.$store.dispatch('compras/cotizacion/paginate', {
                            params: {scope: 'areasCompradorasAsignadas', sort: 'numero_folio', order: 'DESC', include: ['solicitud', 'empresa']}
                        })
                            .then(data => {
                                this.$store.commit('compras/cotizacion/SET_COTIZACIONES', data.data);
                                this.$store.commit('compras/cotizacion/SET_META', data.meta);
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
        },
        computed: {
            cotizacion() {
                return this.$store.getters['compras/cotizacion/currentCotizacion']
            }
        }
    }
</script>

<style scoped>

</style>
