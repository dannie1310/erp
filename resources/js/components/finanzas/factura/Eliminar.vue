<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar Factura" v-show="borrar">
            <i class="fa fa-trash"></i>
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
                        <div class="col-12">
                                <div class="form-group row error-content">
                                            <label for="motivo" class="col-sm-2 col-form-label">Motivo:</label>
                                        <div class="col-sm-10">
                                            <textarea
                                                name="motivo"
                                                id="motivo"
                                                class="form-control"
                                                v-model="motivo"
                                                v-validate="{required: false}"
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
                        <button type="submit" class="btn btn-danger" @click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
export default {
    name: "eliminar-factura",
    props: ['id','pagina','borrar'],
    data() {
        return {
            motivo:'',
            cargando: false,
        }
    },
    methods: {
        destroy() {
            return this.$store.dispatch('finanzas/factura/delete', {
                id: this.id,
                params: {data: [this.$data.motivo]}
            })
            .then(() => {
                this.$store.dispatch('finanzas/factura/paginate', {params: {sort: 'id_transaccion', order: 'desc', include: ['contra_recibo','empresa']}})
                .then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURAS', data.data);
                    this.$store.commit('finanzas/factura/SET_META', data.meta);
                })
            }).finally( ()=>{
                $(this.$refs.modal).modal('hide');
            });
        },
        find(id) {
            this.motivo = '';
            this.cargando = true;
                this.$store.commit('finanzas/factura/SET_FACTURA', null);
                return this.$store.dispatch('finanzas/factura/find', {
                    id: id,
                }).then(data => {
                    this.$store.commit('finanzas/factura/SET_FACTURA', data);
                    $(this.$refs.modal).modal('show')
                }).finally(() => {
                    this.cargando = false;
                })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(this.motivo === '') {
                        swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                    }
                    else {
                        this.destroy()
                    }
                }
            });
        },
    },
    computed:{
        factura() {
                return this.$store.getters['finanzas/factura/currentFactura']
            }
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>