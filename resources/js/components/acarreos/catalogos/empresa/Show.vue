<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR EMPRESA</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cargando">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row justify-content-end">
                              <!--  <historico v-bind:historicos="origen.historicos.data" v-bind:id="id" v-if="origen" />-->
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <br>
                                </div>
                            </div>
                            <div class="row" v-if="empresa">
                                <div class="col-md-12">
                                   <div class="form-group row">
                                       <label class="col-md-2 col-form-label">Razón Social:</label>
                                       <div class="col-md-10">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="empresa.razon_social" />
                                       </div>
                                   </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">RFC:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="empresa.rfc" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Fecha Registro:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="empresa.fecha_registro" />
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <div class="form-group row">
                                         <label class="col-md-2 col-form-label">Registró:</label>
                                        <div class="col-md-7">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="empresa.nombre_registro" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Estatus:</label>
                                        <div class="col-md-2">
                                            <span class="badge" :style="{'background-color': empresa.estado_color}">{{ empresa.estado_format }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
   // import Historico from "./ShowHistorico";
    export default {
        name: "origen-show",
        props: ['id'],
     //   components: {Historico},
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('acarreos/empresa/SET_EMPRESA', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('acarreos/empresa/find', {
                    id: this.id,
                    params: ''//{include : 'historicos'}
                }).then(data => {
                    this.$store.commit('acarreos/empresa/SET_EMPRESA', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            empresa() {
                return this.$store.getters['acarreos/empresa/currentEmpresa']
            }
        }
    }
</script>

<style scoped>

</style>
