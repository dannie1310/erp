<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
        <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-eye" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-factura"> <i class="fa fa-eye"></i> FACTURA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="factura">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label for="fecha">Fecha:</label>
                                    {{factura.fecha_cr}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group error-content">
                                    <label for="fecha">Empresa:</label>
                                    {{factura.empresa}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <!--Referencia-->
                                <div class="form-group error-content">
                                    <label for="referencia">Folio:</label>
                                    {{factura.referencia}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <!--Rubro-->
                                <div class="form-group error-content">
                                    <label >Rubro de Factura:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label >Emisión:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label>Vencimiento:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label >Total:</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    <label >Moneda:</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <!--Rubro-->
                                <div class="form-group error-content">
                                    {{factura.rubro}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">
                                    {{factura.fecha_format}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">

                                    {{factura.vencimiento_format}}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">

                                    {{factura.monto_format}}

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content">

                                    {{factura.moneda}}
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group error-content">
                                    <label for="observaciones">Observaciones:</label>
                                    {{factura.observaciones}}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{factura.datos_registro}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Comentario: {{factura.comentario}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="observaciones">Tipo de Cambio:</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="observaciones">USD:</label>
                                    19.50
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group error-content">
                                    <label for="observaciones">EURO:</label>
                                    21.5
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="observaciones">Subtotal:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.subtotal_format}} </div>
                            </div>
                            <!-- <div class="col-md-5"></div> -->

                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="observaciones">Fondo de Garantía:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> $ 19.50 </div>
                            </div>
                            <!-- <div class="col-md-5"></div> -->

                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="observaciones">IVA Subtotal:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.impuesto_format}}</div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Ret. IVA (4%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_iva_4_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Ret. IVA (6%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_iva_6_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Ret. IVA (2/3):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right">{{factura.complemento.ret_iva_10_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">IVA A Pagar:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.impuesto_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">IEPS:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ieps_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Imp. Hospedaje:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.imp_hosp_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Ret. ISR (10%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_isr_10_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Retenciones Subcontratos:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> $ 19.50  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Devolución de Retenciones Subcontratos:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> $ 19.50  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="observaciones">Total:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.total_format}} </div>
                            </div>
                            <div class="col-md-5"></div>
                            
                        </div>
                        


                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Show",
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            find(id) {
                this.cargando = true;
                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: id,
                    params: {include: ['complemento']}
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                })
            }
        },
        computed: {
            factura() {
                return this.$store.getters['finanzas/factura/currentFactura']
            }
        }
    }
</script>

<style scoped>

</style>