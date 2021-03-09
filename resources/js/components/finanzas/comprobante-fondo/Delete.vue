<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> ELIMINAR COMPROBANTE DE FONDO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="fondo">
                            <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <h5>Folio: &nbsp; <b>{{fondo.numero_folio_format}}</b></h5>
                                        </div>
                                        <div class="col-md-6">
                                        </div>
                                    </div>
                                    <div class="table-responsive col-md-12">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td class="bg-gray-light"><b>Fecha:</b></td>
                                                <td class="bg-gray-light"> {{fondo.fecha_format}} </td>
                                                <td class="bg-gray-light"><b>Fondo:</b></td>
                                                <td class="bg-gray-light">{{fondo.fondo.descripcion}}</td>
                                                <td class="bg-gray-light"><b>Referencia:</b></td>
                                                <td class="bg-gray-light">{{fondo.referencia}}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-gray-light"><b>Concepto:</b></td>
                                                <td class="bg-gray-light" colspan="5">{{fondo.concepto ? fondo.concepto.path : '' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="bg-gray-light"><b>Usuario Registró:</b></td>
                                                <td class="bg-gray-light" colspan="3">{{fondo.usuario_registro}}</td>
                                                <td class="bg-gray-light"><b>Fecha / Hora de Registro:</b></td>
                                                <td class="bg-gray-light">{{fondo.fecha_registro}}</td>
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
                                                    <th class="index_corto">#</th>
                                                    <th>Concepto</th>
                                                    <th class="numerico">Cantidad</th>
                                                    <th class="numerico">Precio</th>
                                                    <th class="numerico">Monto</th>
                                                    <th class="destino">Destino</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(partida, i) in fondo.partidas.data">
                                                    <td style="text-align: center" class="index_corto">{{i+1}}</td>
                                                    <td style="text-align: left">{{partida.referencia }}</td>
                                                    <td style="text-align: right" class="numerico">{{ partida.cantidad_format }}</td>
                                                    <td style="text-align: right" class="numerico">{{ partida.importe_format }}</td>
                                                    <td style="text-align: right" class="numerico">$ {{ partida.monto_format }}</td>
                                                    <td style="text-align: left" class="destino">{{ partida.concepto ? partida.concepto.descripcion : '' }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label class="col-form-label">Observaciones: </label>
                                            <input
                                                type="text"
                                                disabled="true"
                                                v-model="fondo.observaciones"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3" align="left">
                                            <div class="table-responsive col-md-12">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-borderless">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Subtotal:</th>
                                                                    <td align="right">{{fondo.monto_format}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>IVA:</th>
                                                                    <td align="right">{{fondo.impuesto_format}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total:</th>
                                                                    <td align="right">{{fondo.total_format}}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                                     <div class="error-label" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                  </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                                        <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''" v-on:click="eliminar">Eliminar</button>
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
    export default {
        name: "comprobante-fondo-delete",
        props: ['id'],
        data(){
            return{
                cargando: false,
                motivo: '',
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('finanzas/comprobante-fondo/SET_FONDO', null);
                return this.$store.dispatch('finanzas/comprobante-fondo/find', {
                    id: this.id,
                    params:{
                        include: ['fondo', 'partidas.concepto', 'concepto']
                    }
                }).then(data => {
                    this.$store.commit('finanzas/comprobante-fondo/SET_FONDO', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
                    .finally(()=> {
                        this.cargando = false;
                    })
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            },
            eliminar() {
                return this.$store.dispatch('finanzas/comprobante-fondo/eliminar', {
                    id: this.id,
                    params: {data: [this.$data.motivo]}
                })
                    .then(data => {
                        this.$store.commit('finanzas/comprobante-fondo/UPDATE_FONDO', data)
                        this.salir();
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
        },
        computed: {
            fondo() {
                return this.$store.getters['finanzas/comprobante-fondo/currentFondo']
            },
        }
    }
</script>

<style scoped>

</style>
