<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row" v-if="empresa" v-for="archivo in empresa.archivos.data">
                    <!-- <div class="col-md-12"> -->
                        <!-- <br> -->
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



                        <!-- <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th >Documento</th>
                                        <th >Estatus</th>
                                        <th >Nombre Archivo</th>
                                        <th >Usuario Cargo</th>
                                        <th >Fecha Hora Carga</th>
                                        <th >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div> -->
                    <!-- </div> -->
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
        }
    },
    mounted() {
        // this.getSecciones();
    },
    methods: {
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
        }
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