<template>
    <span>
        <div class="card">
            <div class="card-body">
                <!-- <div class="row" v-if="empresa" v-for="archivo in empresa.archivos.data">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Documento:</label>
                                    {{archivo.tipo_archivo_descripcion}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Estatus:</label>
                                    {{archivo.estatus}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Sección:</label>
                                    {{archivo.seccion}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Archivo:</label>
                                    {{archivo.nombre_archivo_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Ver"><i class="fa fa-upload"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Usuario Cargo:</label>
                                    {{archivo.registro}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-group error-content">
                                    <label for="numero">Fecha Hora Carga:</label>
                                    {{archivo.fecha_registro_format}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-1 text-center" >
                            <hr style="color: #0056b2; margin-top:auto;" width="90%" size="10" />
                        </div>
                </div> -->
                <!-- Segunda version con table -->

                <div class="row" v-if="empresa" >
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th class="index_corto">#</th> -->
                                        <th >Documento</th>
                                        <th >Estatus</th>
                                        <th >Sección</th>
                                        <th >Nombre Archivo</th>
                                        <th >Usuario Cargo</th>
                                        <th >Fecha Hora Carga</th>
                                        <th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="archivo in empresa.archivos.data">
                                        <td :title="archivo.tipo_archivo_descripcion">{{archivo.tipo_archivo_descripcion_corta}}</td>
                                        <td>{{archivo.estatus}}</td>
                                        <td>{{archivo.seccion}}</td>
                                        <td>{{archivo.nombre_archivo_format}}</td>
                                        <td>{{archivo.registro}}</td>
                                        <td>{{archivo.fecha_registro_format}}</td>
                                        <td>
                                            <div class="btn-group">
                                            <button @click="modalCarga(archivo)" type="button" class="btn btn-sm btn-outline-primary" title="Ver"><i class="fa fa-upload"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CARGAR ARCHIVO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <label for="carga_bitacora" class="col-lg-12 col-form-label">Cargar {{archivo.tipo_archivo_descripcion}}</label>
                                <div class="col-lg-12">
                                    <input type="file" class="form-control" id="cargar_file"
                                            @change="onFileChange"
                                            row="3"
                                            v-validate="{required:true, ext: ['pdf']}"
                                            name="cargar_file"
                                            data-vv-as="Cargar"
                                            ref="cargar_file"
                                            :class="{'is-invalid': errors.has('cargar_file')}"
                                    >
                                    <div class="invalid-feedback" v-show="errors.has('cargar_file')">{{ errors.first('cargar_file') }} (PDF)</div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        <button @click="upload" type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "proveedor-edit-tab-general",
    props: ['id'],
    data(){
        return{
            documentos:[],
            archivo:'',
            file:'',
            file_name:'',
        }
    },
    mounted() {
        // this.getSecciones();
    },
    methods: {
        createImage(file, tipo) {
            var reader = new FileReader();
            var vm = this;
            reader.onload = (e) => {
                vm.file = e.target.result;
            };
            reader.readAsDataURL(file);

        },
        onFileChange(e){
            this.file = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            if(e.target.id == 'cargar_file') {
                this.file_name = files[0].name;
                this.createImage(files[0]);
            }
            // setTimeout(() => {
            //     this.validate()
            // }, 500);
        },
        find() {
            return this.$store.dispatch('padronProveedores/empresa/getDoctosGenerales', {
                id: this.id,
                params: {include: []}
            }).then(data => {
                this.documentos = data;
                this.cargando = false;
            })
        },
        getSecciones(){
            this.cargando = true;
            this.$store.commit('padronProveedores/ctg-seccion/SET_SECCIONES', null);
            return this.$store.dispatch('padronProveedores/ctg-seccion/index', {
                id: this.id,
                params: {include: [], sort: 'id', order: 'asc'}
            }).then(data => {
                this.$store.commit('padronProveedores/ctg-seccion/SET_SECCIONES', data.data);
            })
        },
        modalCarga(archivo){
            this.archivo = archivo;
            this.file = null;
            this.file_name = '';
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        upload(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('archivo',  this.file_interbancario);
            formData.append('archivo_nombre',  this.file_interbancario_name);
            formData.append('id_empresa',  this.id);
            formData.append('id_archivo',  this.archivo.id);
        },
    },
    computed: {
        empresa(){
            return this.$store.getters['padronProveedores/empresa/currentEmpresa'];
        },
    }

}
</script>

<style>

</style>