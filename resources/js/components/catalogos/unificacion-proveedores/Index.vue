<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" class="btn btn-app btn-info pull-right" v-if="$root.can('registrar_unificacion_proveedores')">
                <i class="fa fa-plus"></i> Registrar
            </button>
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>

</template>

<script>
    export default {
    name: "unificacion-proveedores-index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', sortable: false },
                { title: 'Razón Social', field: 'id_empresa_unificadora', sortable: true },
                { title: 'R.F.C.', field: 'rfc',  sortable: true },
                { title: 'Registro', field: 'usuario_registro', sortable: true },
                { title: 'Fecha de Registro', field: 'FechaHoraRegistro', sortable: true },
            ],
            data: [],
            total: 0,
            query: {sort: 'id', order: 'desc'},
            query: {include: ['usuario','empresa'], sort: 'id', order: 'desc'},
            estado: "",
            cargando: false
        }
    },

    mounted() {
        this.$Progress.start();
        this.paginate()
            .finally(() => {
                this.$Progress.finish();
            })
    },
    methods: {
        create(){
            this.$router.push({name: 'unificacion-proveedores-create'});
        },
        paginate() {
            this.cargando = true;
            return this.$store.dispatch('catalogos/unificacion-proveedores/paginate', { params: this.query})
                .then(data => {
                    this.$store.commit('catalogos/unificacion-proveedores/SET_UNIFICACIONES', data.data);
                    this.$store.commit('catalogos/unificacion-proveedores/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
        }
    },
    computed: {
        unificacionProveedores(){
            return this.$store.getters['catalogos/unificacion-proveedores/unificacionProveedores'];
        },
        meta(){
            return this.$store.getters['catalogos/unificacion-proveedores/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        }
    },
    watch: {
         unificacionProveedores: {
            handler(unificacionProveedores) {
                let self = this
                self.$data.data = []
                self.$data.data = unificacionProveedores.map((unificacion, i) => ({
                    index: (i + 1) + self.query.offset,
                    id_empresa_unificadora: unificacion.empresa.razon_social,
                    rfc: unificacion.empresa.rfc,
                    usuario_registro: unificacion.usuario.nombre,
                    FechaHoraRegistro: unificacion.fecha_format,

                }));
            },
            deep: true
        },
        meta: {
            handler (meta) {
                let total = meta.pagination.total
                this.$data.total = total
            },
            deep: true
        },
    },
    
}
</script>

<style>

</style>