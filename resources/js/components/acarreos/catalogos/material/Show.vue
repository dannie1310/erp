<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR MATERIAL</h5>
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
                            <div class="row" v-if="material">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Descripción:</label>
                                        <div class="col-md-10">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="material.descripcion" />
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Registró:</label>
                                        <div class="col-md-10">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="material.usuario_registro" />
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Fecha Registro:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="material.fecha_registro_format" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Estatus:</label>
                                        <div class="col-md-4">
                                            <span class="badge" :style="{'background-color': material.estado_color}">{{ material.estado_format }}</span>
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
    export default {
        name: "material-show",
        props: ['id'],
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('acarreos/material/SET_MATERIAL', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('acarreos/material/SET_MATERIAL', null);
                return this.$store.dispatch('acarreos/material/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/material/SET_MATERIAL', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            material() {
                return this.$store.getters['acarreos/material/currentMaterial']
            }
        }
    }
</script>

<style scoped>

</style>
