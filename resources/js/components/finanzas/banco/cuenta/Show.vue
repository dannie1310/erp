<template>
    <span>
        <button @click="show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Cuenta">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CONSULTA DE CUENTA BANCARIA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="cuenta">
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Número: </b></label>
                                     {{ cuenta.numero }}
                                    </div>
                            </div>

                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Moneda: </b></label>
                                     {{ cuenta.moneda.nombre }}
                                    </div>
                            </div>

                            <div class="col-md-12  mt-3 text-left" >
                                  <label class="text-secondary">Apertura </label>
                                   <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>

                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Fecha: </b></label>
                                     {{ cuenta.fecha }}
                                    </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><b>Saldo Inicial: </b></label>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <div class="form-group">
                                    {{ cuenta.saldo }}
                                </div>
                            </div>

                            <div class="col-md-12  mt-3 " >
                                   <hr style="color: #0056b2; margin-top:auto;" width="95%" size="10" />
                            </div>

                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Abreviatura: </b></label>
                                     {{ cuenta.abreviatura }}
                                    </div>
                            </div>

                            <div class="col-md-6">
                                 <div class="form-group">
                                        <label><b>Manejo de Chequera: </b></label>
                                     {{ valChequera(cuenta.chequera)  }}
                                    </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><b>Tipo de Cuenta: </b></label>
                                    {{ cuenta.tiposCuentasObra.descripcion }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><b>Saldo Real: </b></label>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <div class="form-group">
                                    {{ cuenta.saldo_real }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                     </div>
                </div>
            </div>
        </div>

    </span>
</template>

<script>
    export default {
        name: "cuenta-show",
        props: ['id'],
        methods:{
            show() {
                this.cargando = true;
                this.$store.commit('cadeco/cuenta/SET_CUENTA', null);
                return this.$store.dispatch('cadeco/cuenta/find', {
                    id: this.id,
                    params: { include: 'moneda,tiposCuentasObra' }
                }).then(data => {
                    this.$store.commit('cadeco/cuenta/SET_CUENTA', data);
                    $(this.$refs.modal).modal('show')
                })
            },
            valChequera(value){
                return value == 1?'Si':'No'
            }
        },
        computed: {
            cuenta() {
                return this.$store.getters['cadeco/cuenta/currentCuenta']
            }
        }
    }
</script>

<style scoped>

</style>
