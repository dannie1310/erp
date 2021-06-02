<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-close"></i> DESACTIVAR EMPRESA</h5>
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
                                <div class="col-md-12" v-if="empresa.estado == 1">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" v-if="empresa">
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
        name: "empresa-desactivar",
        props: ['id'],
        data() {
            return {
                motivo : '',
                cargando : true
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
                this.$store.commit('acarreos/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('acarreos/empresa/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/empresa/SET_EMPRESA', data);
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
                return this.$store.dispatch('acarreos/empresa/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}})
                    .then((data) => {
                        this.$store.commit('acarreos/empresa/UPDATE_EMPRESA', data);
                        $(this.$refs.modal).modal('hide');
                    })
            }
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
