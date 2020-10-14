<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-primary" :disabled="cargando" title="Ver">
            <i style="width:40px;" v-if="!cargando">{{numero_folio}}</i>
            <i class="fa fa-spinner fa-spin" style="width:40px;" v-else></i>
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
                            <div class="col-md-12">
                                <button type="button" @click="prepoliza(factura.poliza.id)" class="btn btn-primary float-right" v-if="factura.poliza && $root.can('consultar_prepolizas_generadas') && factura.estado > 0"> Ver Prepóliza</button>
                            </div>
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
                                    <label for="tipo_cambio">Tipo de Cambio:</label>
                                </div>
                            </div>
                            <span v-if="factura.cambio" v-for="(cambio, i) in factura.cambio.data">
                                <div class="form-group error-content">
                                    <label :for="`cambio[${i}]`">{{cambio.moneda.abreviatura}}:</label>
                                    {{cambio.cambio_format}}&nbsp&nbsp
                                </div>
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="subtotal">Subtotal:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.subtotal_format}} </div>
                            </div>

                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="fondo_garantia">Fondo de Garantía:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.fondo_garantia_format}} </div>
                            </div>

                            <div class="col-md-10">
                                <div class="form-group error-content float-right"><label for="iva_subtotal">IVA Subtotal:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.impuesto_format}}</div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="ret_iva_4">Ret. IVA (4%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_iva_4_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="ret_iva_6">Ret. IVA (6%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_iva_6_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="ret_iva_10">Ret. IVA (2/3):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right">{{factura.complemento.ret_iva_10_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="iva_pagar">IVA A Pagar:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.impuesto_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="ieps">IEPS:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ieps_format}} </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="imp_hosp">Imp. Hospedaje:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.imp_hosp_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="ret_isr_10">Ret. ISR (10%):</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.complemento.ret_isr_10_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="retenciones_subcontratos">Retenciones Subcontratos:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.retenciones_format}}   </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="devoluciones_sbcontrato">Devolución de Retenciones Subcontratos:</label></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group error-content float-right"> {{factura.devoluciones_format}}  </div>
                            </div>
                            <div class="col-md-5"></div>

                            <div class="col-md-5">
                                <div class="form-group error-content float-right"> <label for="total">Total:</label></div>
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
        props: ['id' ,'numero_folio'],
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
                    params: {include: ['complemento', 'cambio.moneda', 'poliza']}
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                })
            },
            prepoliza(id){
                let prepoliza = this.$router.resolve({name: 'poliza-show', params: {id: id}});
                window.open(prepoliza.href, '_blank');
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
