<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR IMPRESORA</h5>
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
                                <historico v-bind:historicos="impresora.historicos.data" v-bind:id="id" v-if="impresora" />
                            </div>
                             <div class="row">
                                <div class="col-md-12">
                                    <br>
                                </div>
                            </div>
                            <div class="row" v-if="impresora">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">MAC:</label>
                                        <div class="col-md-5">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="impresora.mac" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Marca:</label>
                                        <div class="col-md-5">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="impresora.marca" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Modelo:</label>
                                        <div class="col-md-5">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="impresora.modelo" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Registró:</label>
                                        <div class="col-md-5">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="impresora.usuario_registro" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Fecha Registro:</label>
                                        <div class="col-md-5">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="impresora.fecha_registro_format" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Estatus:</label>
                                        <div class="col-md-3">
                                            <span class="badge" :style="{'background-color': impresora.estado_color}">{{ impresora.estado_format }}</span>
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
    import Historico from "./ShowHistorico";
    export default {
        name: "impresora-show",
        props: ['id'],
        components: {Historico},
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('acarreos/impresora/SET_IMPRESORA', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/impresora/SET_IMPRESORA', null);
                return this.$store.dispatch('acarreos/impresora/find', {
                    id: this.id,
                    params: {include : 'historicos'}
                }).then(data => {
                    this.tipo = data.id_tipo
                    this.$store.commit('acarreos/impresora/SET_IMPRESORA', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            impresora() {
                return this.$store.getters['acarreos/impresora/currentImpresora']
            }
        }
    }
</script>

<style scoped>

</style>
