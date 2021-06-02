<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> DESACTIVAR IMPRESORA</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!impresora">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>MAC:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{impresora.mac}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Marca:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{impresora.marca}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Modelo:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{impresora.modelo}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Fecha:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{impresora.fecha_registro_format}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Estado:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <span class="badge" :style="{'background-color': impresora.estado_color}">{{ impresora.estado_format }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6><b>Usuario Registró:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <h6>{{impresora.usuario_registro}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" v-if="impresora.estado == 1">
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                            <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" v-if="impresora">
                            <i class="fa fa-save"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    export default {
        name: "immpresora-desactivar",
        props: ['id'],
        data() {
            return {
                motivo : '',
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.motivo = '';
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/impresora/SET_IMPRESORA', null);
                return this.$store.dispatch('acarreos/impresora/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/impresora/SET_IMPRESORA', data);
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.desactivar()
                    }
                });
            },
            desactivar() {
                return this.$store.dispatch('acarreos/impresora/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}})
                    .then((data) => {
                        this.$store.commit('acarreos/impresora/UPDATE_IMPRESORA', data);
                        $(this.$refs.modal).modal('hide');
                    })
            }
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
