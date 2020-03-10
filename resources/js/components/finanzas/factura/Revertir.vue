<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-info" title="Revertir Revisión">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-repeat" v-else></i>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-info" v-on:click="revertir" >Revertir</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Revertir",
        props: ['id'],
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: this.id,
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).appendTo('body');
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                })
            },
            revertir() {
                return this.$store.dispatch('finanzas/factura/revertir', {
                    id: this.id,
                }).then(data => {
                    this.$store.commit('finanzas/factura/UPDATE_FACTURA', data);
                    $(this.$refs.modal).modal('hide')
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
