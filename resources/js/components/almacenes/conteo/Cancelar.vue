<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar">
            <i class="fa fa-ban"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CANCELAR SOLICITUD DE BAJA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11 form-group row error-content">
                                    <b>Motivo de Cancelación: </b>
                            </div>
                         </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="form-group row error-content">
                                    <div class="col-sm-11">
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Cancelar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "conteo-cancelar",
        props: ['id'],
        data() {
            return {
                observaciones: ''
            }
        },
        methods: {
            find(id) {
                this.$store.commit('almacenes/conteo/SET_CONTEO', null);
                return this.$store.dispatch('almacenes/conteo/find', {
                    id: id,
                    params: { include: ['marbete'] }
                }).then(data => {
                    this.$store.commit('almacenes/conteo/SET_CONTEO', data);
                    $(this.$refs.modal).modal('show');
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.cancelar()
                    }
                });
            },
            cancelar() {
                return this.$store.dispatch('finanzas/solicitud-baja-cuenta-bancaria/cancelar', {
                    id: this.id,
                    params: { include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza','movimientos.usuario','mov_estado'], data:[this.$data.observaciones]}
                }).then(data => {
                    this.$store.commit('finanzas/solicitud-baja-cuenta-bancaria/UPDATE_CUENTA', data)
                    $(this.$refs.modal).modal('hide');
                })
                    .finally( ()=>{
                        this.cargando = false;
                    });
            }
        },
        computed: {
            solicitudBaja() {
                return this.$store.getters['finanzas/solicitud-baja-cuenta-bancaria/currentCuenta'];
            }
        }
    }
</script>

<style scoped>

</style>