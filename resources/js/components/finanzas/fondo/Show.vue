<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" title="Ver Fondo">
            <i class="fa fa-eye"></i>
        </button>
          <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> INFORMACIÓN DE FONDO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                        <div class="modal-body">
                            <div>
                                <div v-if="fondoFijo">
                                      <div class="row" v-if="fondoFijo">
                                          <div class="col-md-12">
                                            <div class="form-group error-content">
                                                <div class="form-group">
                                                    <label><b>Fecha:</b></label>
                                                    {{ fondoFijo.fecha_format }}
                                                </div>
                                            </div>
                                          </div>
                                               <div class="col-md-12">
                                                    <div class="form-group error-content">
                                                        <div class="form-group">
                                                            <label><b>Responsable:</b></label>
                                                            {{ fondoFijo.nombre }}
                                                        </div>
                                                    </div>
                                                </div>
                                           <div class="col-md-12">
                                                    <div class="form-group error-content">
                                                        <div class="form-group">
                                                            <label><b>Tipo de Fondo:</b></label>
                                                            {{ fondoFijo.tipo_fondo.descripcion }}
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-md-12">
                                                    <div class="form-group error-content">
                                                        <div class="form-group">
                                                            <label><b>Tipo de Gasto:</b></label>
                                                            {{ fondoFijo.costo.descripcion }}
                                                        </div>
                                                    </div>
                                                </div>

                                           <div class="col-md-12">
                                                    <div class="form-group error-content">
                                                        <div class="form-group">
                                                            <label><b>Saldo:</b></label>
                                                            {{ `$ ${parseFloat(fondoFijo.saldo).formatMoney(2)}`}}
                                                        </div>
                                                    </div>
                                                </div>
                                           <div class="col-md-12" v-id="fondoFijo">
                                                    <div class="form-group error-content">
                                                        <div class="form-group" v-if="fondoFijo.fondo_obra === '0'">
                                                            <label><b>Fondo de Obra:</b></label>
                                                            No
                                                        </div>
                                                          <div class="form-group" v-if="fondoFijo.fondo_obra === '1'">
                                                            <label><b>Fondo de Obra:</b></label>
                                                            Si
                                                        </div>
                                                    </div>
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
        name: "fondo-show",
        props: ['id'],
        data(){
          return{
              fondo: null,
              cargando: false,
          }
        },
        methods: {
            find() {
                 this.cargando = true;
                this.$store.commit('cadeco/fondo/SET_FONDO', null);
                return this.$store.dispatch('cadeco/fondo/find', {
                    id: this.id,
                    params:{
                        include: ['tipo_fondo','costo'], scope:'ConResponsable', order: 'desc'
                    }
                }).then(data => {
                    this.$store.commit('cadeco/fondo/SET_FONDO', data);
                    $(this.$refs.modal).modal('show')
                })

            }
        },
        computed: {
            fondoFijo() {
                return this.$store.getters['cadeco/fondo/currentFondo']
             }
        }
    }
</script>

<style scoped>

</style>