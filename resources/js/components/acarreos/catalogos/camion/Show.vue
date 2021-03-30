<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show" v-if="$root.can('consultar_camion')">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR CAMIÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="!camion">
                                <div class="row" >
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <div class="col-md-2">
                                                    <label>Economico:</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input disabled="true"
                                                           type="text"
                                                           name="economico"
                                                           class="form-control"
                                                           v-model="camion.economico" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <div class="form-group">
                                                    <label>Tipo de Origen:</label>
                                                    <input disabled="true"
                                                           type="text"
                                                           name="tipo"
                                                           class="form-control"
                                                           id="tipo"
                                                           v-model="origen.tipo" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12 error-content">
                                                <label for="descripcion" class="col-form-label">Descripción:</label>
                                                <input style="text-transform:uppercase;"
                                                       disabled="true"
                                                       type="text"
                                                       name="descripcion"
                                                       class="form-control"
                                                       id="descripcion"
                                                       v-model="origen.descripcion" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "origen-show",
        props: ['id'],
        data() {
            return {

            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/camion/SET_CAMION', null);
                return this.$store.dispatch('acarreos/camion/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/camion/SET_CAMION', data);
                })
            },
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
