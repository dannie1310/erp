<template>
    <span>
        <button class="btn btn-app pull-right dropdown-toggle"
                type="button"
                id="dropdownMenuButton"
                data-toggle="dropdown"
                data-boundary="window"
                aria-haspopup="true"
                aria-expanded="false">
            <span><i class="fa fa-plus"></i></span>
            Registrar
        </button>
        <div class="dropdown-menu">
            <button @click="load" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Cargar" >
                <i class="fa fa-upload"></i> Cargar Layout
            </button>
            <button @click="registrar" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Registrar" >
                <i class="fa fa-pencil"></i> Ir a Formulario
            </button>
        </div>
        <div class="modal fade" ref="modal_carga" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-carga"> <i class="fa fa-upload"></i> CARGAR FACTURA CON CFDI</h5>
                        <button type="button" class="close" v-on:click="cerrarModalCarga" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row justify-content-between">
                                        <div class="col-md-12">
                                            <div class="col-lg-12">
                                                <input type="file" class="form-control" id="carga_layout"
                                                       @change="onFileChange"
                                                       row="3"
                                                       v-validate="{ ext: ['xml']}"
                                                       name="carga_layout"
                                                       data-vv-as="Layout"
                                                       ref="carga_layout"
                                                       :class="{'is-invalid': errors.has('carga_layout')}">
                                                <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xml)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="cerrarModalCarga" :disabled="cargando"><i class="fa fa-times"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="getLayoutData()" :disabled="errors.has('carga_layout') || file_carga === null">
                                <i class="fa fa-spin fa-spinner" v-if="procesando"></i>
                                <i class="fa fa-upload" v-else ></i> Cargar
                            </button>

                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- <div class="modal fade" ref="modal_errores" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal modal-xl" >
                <div class="modal-content" v-if="data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-carga"> <i class="fa fa-close"></i> ERRORES EN CFDI DE LA FACTURA</h5>
                        <button type="button" class="close" v-on:click="cerrarModalErrores" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="pull-right" style="color:red;"><h5>Las siguientes partidas tienen error en: {{data.errores_partidas}}</h5></div>
                        </div>
                        <br />
                        <div class="row">
                            <div  class="col-12">
                                <div class="table-responsive" style="overflow-y: auto; height:350px">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="c100">Clave</th>
                                                <th >Descripción</th>
                                                <th class="c100">Unidad</th>
                                                <th class="c100">Cantidad</th>
                                                <th >Destinos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(partida, i) in data.contratos" v-if="!partida.partida_valida">
                                                <td>{{partida.clave}}</td>
                                                <td :style="Object.keys(partida.tipo_error).length > 0 && partida.tipo_error.descripcion?`color: red;`:``">{{partida.descripcion}}</td>
                                                <td>{{partida.unidad}} </td>
                                                <td :style="Object.keys(partida.tipo_error).length > 0 && partida.tipo_error.cantidad?`color: red;`:``">{{partida.cantidad}} </td>
                                                <td :style="Object.keys(partida.tipo_error).length > 0 && partida.tipo_error.destino?`color: red;`:``">{{partida.destino_path}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="cerrarModalErrores" :disabled="cargando"><i class="fa fa-times"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>-->
    </span>
</template>

<script>
    export default {
        name: "registrar-factura-cfdi",
        props: ['cargando'],
        data() {
            return {
                procesando:false,
                file_carga : null,
                file_carga_name : '',
                data: null,
            }
        },
        mounted(){
        },
        methods:{
            load() {
                this.file = null;
                this.$validator.errors.clear();

                $(this.$refs.modal_carga).appendTo('body')
                $(this.$refs.modal_carga).modal('show');
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_carga = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            onFileChange(e){
                this.file_carga = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_carga_name = files[0].name;
                this.createImage(files[0]);

            },
            getLayoutData(){
                this.procesando = true;
                var formData = new FormData();
                formData.append('facturas',  this.file_carga);
                formData.append('nombre_archivo',  this.file_carga_name);
                return this.$store.dispatch('seguimiento/factura/cargarCFDI',{
                    data: formData, config: { params: { _method: 'POST'}}
                })
                    .then(data => {
                        if(data.partidas_con_error){
                            this.data = data;
                            $(this.$refs.modal_errores).appendTo('body')
                            $(this.$refs.modal_errores).modal('show');
                        }else{
                            this.procesando = false;
                            this.cerrarModalCarga();
                            this.$router.push({name: 'proyectado-layout-create', params: {partidas:data.contratos}});
                        }
                    }).finally(() => {
                        this.procesando = false;
                        this.cerrarModalCarga();
                    });
            },
            registrar(){
                this.cerrarModalCarga();
                this.$router.push({name: 'factura-seg-create'});
            },
            cerrarModalCarga(){
                if(this.$refs.carga_layout.value && this.$refs.carga_layout.value !== ''){
                    this.$refs.carga_layout.value = '';
                }
                this.file_carga = null;
                $(this.$refs.modal_carga).modal('hide');
                this.$validator.reset();
            },
            cerrarModalErrores(){
                if(this.$refs.carga_layout.value && this.$refs.carga_layout.value !== ''){
                    this.$refs.carga_layout.value = '';
                }
                this.file_carga = null;
                $(this.$refs.modal_errores).modal('hide');
                this.$validator.reset();
            },
        },

    }
</script>

<style scoped>
    .cantidad_invalida {
        color: red;
    }
</style>
