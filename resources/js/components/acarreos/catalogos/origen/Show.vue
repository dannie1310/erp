<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR ORIGEN</h5>
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
                                <historico v-bind:historicos="origen.historicos.data" v-bind:id="id" v-if="origen" />
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <br>
                                </div>
                            </div>
                            <div class="row" v-if="origen">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Clave:</label>
                                        <div class="col-md-2">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.clave_format" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Tipo:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.tipo" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Tipo de origen:</label>
                                        <div class="col-md-2">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.tipo_origen" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Descripción:</label>
                                        <div class="col-md-10">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.descripcion" />
                                        </div>

                                    </div>
                                </div>

                            </div> <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Registró:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.usuario_registro" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Fecha Registro:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="origen.fecha_registro_format" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Estatus:</label>
                                        <div class="col-md-1">
                                            <span class="badge" :style="{'background-color': origen.estado_color}">{{ origen.estado_format }}</span>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    import Historico from "./ShowHistorico";
    export default {
        name: "origen-show",
        props: ['id'],
        components: {Historico},
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('acarreos/tiro/SET_TIRO', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.tipo= '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/origen/SET_ORIGEN', null);
                return this.$store.dispatch('acarreos/origen/find', {
                    id: this.id,
                    params: {include : 'historicos'}
                }).then(data => {
                    this.tipo = data.id_tipo
                    this.$store.commit('acarreos/origen/SET_ORIGEN', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            origen() {
                return this.$store.getters['acarreos/origen/currentOrigen']
            }
        }
    }
</script>

<style scoped>

</style>
