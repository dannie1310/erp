<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> DESACTIVAR CAMIÓN</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="!camion">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Camión:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{camion.economico}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Placa:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{camion.placa}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Fecha:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h6>{{camion.fecha_registro}}</h6>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <h6><b>Estado:</b></h6>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <span class="badge" :style="{'background-color': camion.estado_color}">{{ camion.estado_format }}</span>
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
                                                <h6>{{camion.nombre_registro}}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                    <button type="submit" class="btn btn-primary" @click="desactivar" :disabled="errors.count() > 0" v-if="camion">
                                        <i class="fa fa-save"></i>Guardar
                                    </button>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "camion-desactivar",
        props: ['id'],
        data() {
            return {
                cargando : true,
                motivo : ''
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
                this.$store.commit('acarreos/camion/SET_CAMION', null);
                return this.$store.dispatch('acarreos/camion/find', {
                    id: this.id,
                }).then(data => {
                    this.$store.commit('acarreos/camion/SET_CAMION', data);
                }).finally(() => {
                    this.cargando = false;
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
                return this.$store.dispatch('acarreos/camion/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}
                })
                    .then((data) => {
                        this.$store.commit('acarreos/camion/UPDATE_CAMION', data);
                        this.salir()
                    })
            }
        },
        computed: {
            camion() {
                return this.$store.getters['acarreos/camion/currentCamion']
            }
        }
    }
</script>

<style scoped>

</style>
