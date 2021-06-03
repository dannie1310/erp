<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger" title="Desactivar">
            <i class="fa fa-close"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modl-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-close"></i> DESACTIVAR MARCA</h5>
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
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-md-10">
                                                    <h6><b>Descripción:</b></h6>
                                                    <h6>{{marca.descripcion}}</h6>
                                                </div>
                                                <div class="col-md-2">
                                                    <h6><b>Estado:</b></h6>
                                                    <span class="badge" :style="{'background-color': marca.estado_color}">{{ marca.estado_format }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
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
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="desactivar" :disabled="errors.count() > 0" v-if="marca">
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
        name: "marca-desactivar",
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
                this.$store.commit('acarreos/marca/SET_MARCA', null);
                return this.$store.dispatch('acarreos/marca/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/marca/SET_MARCA', data);
                }).finally(() => {
                    this.cargando = false;
                })
            },
            desactivar() {
                return this.$store.dispatch('acarreos/marca/desactivar', {
                    id: this.id,
                    params: {motivo: this.motivo}})
                    .then((data) => {
                        this.$store.commit('acarreos/marca/UPDATE_MARCA', data);
                        $(this.$refs.modal).modal('hide');
                    })
            }
        },
        computed: {
            marca() {
                return this.$store.getters['acarreos/marca/currentMarca']
            }
        }
    }
</script>

<style scoped>

</style>
