<template>
    <span>
        <button @click="open()" type="button" class="btn btn-sm btn-outline-danger" :disabled="cargando" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> DETALLES DE LA COTIZACIÓN</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <cotizacion-proveedor-partial-show v-bind:id_invitacion="this.id_invitacion"  @cargaFinalizada="iniciar" />
                                </div>
                            </div>
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
                            <button type="button" class="btn btn-secondary" @click="salir">
                                <i class="fa fa-times-circle"></i>
                                Cerrar</button>
                            <button type="submit" class="btn btn-danger" :disabled="errors.count() > 0 || fin_carga == 0 || motivo == ''">
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
    import CotizacionProveedorPartialShow from "./partials/PartialShow";
    export default {
        name: "cotizacion-proveedor-delete",
        components: {CotizacionProveedorPartialShow},
        props: ['id_invitacion'],
        data(){
            return{
                cargando : false,
                motivo : '',
                fin_carga: 0
            }
        },
        methods: {
            open()
            {
                this.motivo = '';
                this.cargando = true;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show')
            },
            iniciar() {
                this.fin_carga = 1
                this.cargando = false
            },
            salir()
            {
                $(this.$refs.modal).modal('hide');
                this.cargando = false;
            },
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('compras/cotizacion/eliminarProveedor', {
                    id: this.id_invitacion,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.salir();
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
