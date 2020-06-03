<template>
    <div class="row">
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
    name: "orden-compra-index",
    data() {
        return {
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', sortable: false },
                { title: 'Razón Social', field: 'id_empresa_unificadora', thComp: require('../../globals/th-Filter').default, sortable: true },
                { title: 'R.F.C.', field: 'rfc', thComp: require('../../globals/th-Filter').default, sortable: true },
                { title: 'Registro', field: 'usuario_registro', sortable: true },
                { title: 'Fecha de Registro', field: 'FechaHoraRegistro', sortable: true },
            ],
            data: [],
            total: 0,
            query: {sort: 'id', order: 'desc'},
            // query: {include: ['solicitud','empresa'], sort: 'id_transaccion', order: 'desc'},
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
    
}
</script>

<style>

</style>