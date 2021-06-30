<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-eye"></i> CONSULTAR DETALLE DE AUTORIZACIÓN O RECHAZO DEL PAGO PARA PROVEEDOR NO LOCALIZADO</h5>
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
                            <div class="row" v-if="documento">
                                <div class="col-md-12">
                                    <h5>Datos Remesa:</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Proyecto:</label>
                                        <div class="col-md-10">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.remesaSinScope.proyecto_descripcion" />
                                       </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Año:</label>
                                        <div class="col-md-2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.remesaSinScope.año" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Semana:</label>
                                        <div class="col-md-2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.remesaSinScope.semana" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Folio:</label>
                                        <div class="col-md-2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.remesaSinScope.folio" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Tipo:</label>
                                        <div class="col-md-2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.remesaSinScope.tipo" />
                                       </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h5>Datos Proveedor No Localizado:</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Proveedor:</label>
                                        <div class="col-md-6">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.proveedor" />
                                        </div>
                                        <label class="col-md-1 col-form-label">RFC:</label>
                                        <div class="col-md-3">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.rfc" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h5>Estado de Autorización | Rechazo:</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Estado:</label>
                                        <div class="col-md-2">
                                            <span class="badge" :style="{'background-color': documento.estado_color}">{{ documento.estado_format }}</span>
                                        </div>
                                        <label class="col-md-1 col-form-label" v-if="documento.estado != 0">Fecha:</label>
                                        <div class="col-md-3" v-if="documento.estado == 1">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.fecha" />
                                        </div>
                                        <div class="col-md-3" v-if="documento.estado == 2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.fecha_rechazo" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Usuario Registro:</label>
                                        <div class="col-md-4">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.registro" />
                                        </div>
                                        <label class="col-md-1 col-form-label">Usuario:</label>
                                        <div class="col-md-4" v-if="documento.estado == 1">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.aprobo" />
                                        </div>
                                        <div class="col-md-4" v-if="documento.estado == 2">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.rechazo" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="documento.estado == 2">
                                    <div class="form-group row">
                                        <label class="col-md-1 col-form-label">Motivo:</label>
                                        <div class="col-md-3">
                                           <input disabled="true"
                                                  type="text"
                                                  class="form-control"
                                                  v-model="documento.documento.motivo" />
                                        </div>
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
    export default {
        name: "autorizar-pago-factura-show",
        props: ['id'],
        data() {
            return {
                cargando : true
            }
        },
        methods: {
            salir() {
                this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', null);
                $(this.$refs.modal).modal('hide');
            },
            find() {
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', null);
                return this.$store.dispatch('remesas/documento-no-localizado/find', {
                    id: this.id,
                    params: {include : 'documento.remesaSinScope'}
                }).then(data => {
                    this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTO', data);
                }).finally(()=>
                {
                    this.cargando = false;
                })
            },
        },
        computed: {
            documento() {
                return this.$store.getters['remesas/documento-no-localizado/currentDocumento']
            }
        }
    }
</script>

<style scoped>

</style>
