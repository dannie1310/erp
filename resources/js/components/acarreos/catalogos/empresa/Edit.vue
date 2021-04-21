<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> EDITAR EMPRESA</h5>
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
                                <div class="col-md-12" v-if="empresa.estado == 0">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Desactivó:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="empresa.nombre_desactivo" />
                                        </div>
                                        <label class="col-md-2 col-form-label">Fecha Desactivación:</label>
                                        <div class="col-md-4">
                                            <input disabled="true"
                                                   type="text"
                                                   class="form-control"
                                                   v-model="empresa.fecha_desactivo" />
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
        name: "empresa-edit",
        props: ['id'],
        data() {
            return {
                cargando : true,
                empresa : ''
            }
        },
        methods: {
            salir() {
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                return this.$store.dispatch('acarreos/empresa/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.empresa = data
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        }
    }
</script>

<style scoped>

</style>
