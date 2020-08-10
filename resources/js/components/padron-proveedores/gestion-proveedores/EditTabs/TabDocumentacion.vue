<template>
    <span>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="table-responsive">
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
                            <!-- <button type="button" class="btn btn-secondary pull-right"><i class="fa fa-angle-left"></i>Registrar</button> -->
                        </div>
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
        }
    },
    mounted() {
        this.getSecciones();
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
                this.find();
            })
        }
    },
    computed: {
        secciones(){
            return this.$store.getters['padronProveedores/ctg-seccion/secciones'];
        },
    }

}
</script>

<style>

</style>