<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR OPERADOR</h5>
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
                                <historico v-bind:historicos="operador.historicos.data" v-bind:id="id" v-if="operador" />
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <br>
                                </div>
                            </div>
                            <div class="row" v-if="operador">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Nombre:</label>
                                        <div class="col-md-10">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.nombre" />
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Dirección:</label>
                                        <div class="col-md-10">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.direccion" />
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Número de Licencia:</label>
                                        <div class="col-md-3">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.no_licencia" />
                                        </div>
                                        <label class="col-md-3 col-form-label">Vigencia de Licencia:</label>
                                        <div class="col-md-3">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.licencia_vigencia_format" />
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Registró:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.usuario_registro" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Fecha Registro:</label>
                                        <div class="col-md-3">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="operador.fecha_registro_format" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Estatus:</label>
                                        <div class="col-md-1">
                                            <span class="badge" :style="{'background-color': operador.estado_color}">{{ operador.estado_format }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    import Historico from "./ShowHistorico";
    export default {
        name: "operador-show",
        props: ['id'],
        components: {Historico},
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('acarreos/operador/SET_OPERADOR', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/operador/SET_OPERADOR', null);
                return this.$store.dispatch('acarreos/operador/find', {
                    id: this.id,
                    params: {include : 'historicos'}
                }).then(data => {
                    this.tipo = data.id_tipo
                    this.$store.commit('acarreos/operador/SET_OPERADOR', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            operador() {
                return this.$store.getters['acarreos/operador/currentOperador']
            }
        }
    }
</script>

<style scoped>

</style>
