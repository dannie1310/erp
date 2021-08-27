<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-trash"></i> ELIMINAR PRESUPUESTO CONTRATISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="presupuesto">
                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row col-md-12">
                                            <div class="col-md-6">
                                                <h5>Folio: &nbsp; <b>{{presupuesto.numero_folio}}</b></h5>
                                            </div>
                                        </div>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="bg-gray-light" align="center" colspan="8"><b>{{ presupuesto.razon_social }}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Sucursal:</b></td>
                                                    <td class="bg-gray-light">{{invitacion.descripcion_sucursal}}</td>
                                                    <td class="bg-gray-light"><b>ToTC USD:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_usd_format}}</td>
                                                    <td class="bg-gray-light"><b>ToTC EURO:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_euro_format}}</td>
                                                    <td class="bg-gray-light"><b>ToTC LIBRA:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.tc_libra_format}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-gray-light"><b>Direccion:</b></td>
                                                    <td class="bg-gray-light" colspan="3">{{invitacion.direccion_sucursal}}</td>
                                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                                    <td class="bg-gray-light">{{presupuesto.fecha_format}}</td>
                                                    <td class="bg-gray-light"><b>Importe:</b></td>
                                                    <td class="bg-gray-light">{{'$ ' + (parseFloat(presupuesto.subtotal) + parseFloat(presupuesto.impuesto)).formatMoney(2,'.',',')}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-12">
                                                <h6><b>Detalle de las partidas</b></h6>
                                            </div>
                                        </div>
                                        <div class="row" v-if="presupuesto">
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
                                                    <tbody v-for="(partida, i) in presupuesto.contratos">
                                                    <tr v-if="partida.unidad">
                                                        <td >{{i + 1}}</td>
                                                        <td style="text-align: left" v-html="partida.descripcion"></td>
                                                        <td style="text-align: center">{{partida.unidad}}</td>
                                                        <td style="text-align: center">{{partida.cantidad_original_format}}</td>
                                                        <td style="text-align: center">{{partida.cantidad_presupuestada_format}}</td>
                                                        <td class="money">{{partida.precio_unitario_antes_descuento_format}}</td>
                                                        <td style="text-align: center">{{partida.descuento}}</td>
                                                        <td class="money">{{partida.precio_unitario_despues_descuento_format}}</td>
                                                        <td style="text-align: center">{{partida.moneda}}</td>
                                                        <td class="money">{{partida.total_despues_descuento_partida_mc_format}}</td>
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
                                            <label class="col-sm-4 col-form-label">Subtotal Moneda Conversión (MXN):</label>
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
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="motivo" class="col-md-2 col-form-label">Motivo de eliminación:</label>
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0">Eliminar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "presupuesto-eliminar",
    props: ['id'],
    data() {
        return {
            cargando: false,
            presupuesto: [],
            invitacion: [],
            motivo: ''
        }
    },
    methods: {
        find() {
            this.cargando = true;
            this.motivo = '';
            return this.$store.dispatch('padronProveedores/invitacion/find', {
                id: this.id,
                params: {
                    include: ['presupuesto_proveedor'],
                    scope: ['invitadoAutenticado']
                }
            }).then(data => {
                this.presupuesto = data.presupuesto_proveedor
                this.invitacion = data
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.destroy()
                }
            });
        },
        destroy() {
            return this.$store.dispatch('contratos/presupuesto/deletePresupuestoProveedor', {
                id: this.id,
                params: {data: this.motivo}
            })
            .then(() => {
                return this.$store.dispatch('padronProveedores/invitacion/paginate', {
                    params: this.query
                }).then(data => {
                    this.$store.commit('padronProveedores/invitacion/SET_INVITACIONES', data.data);
                    this.$store.commit('padronProveedores/invitacion/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
    },
    computed: {
        total()
        {
            return '$ ' + (parseFloat(this.presupuesto.subtotal) + parseFloat(this.presupuesto.impuesto)).formatMoney(2,'.',',');
        }
    }
}
</script>
