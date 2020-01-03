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
        name: "proveedor-contratista-index",
        components: {},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'R.F.C', field: 'id_transaccion',thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Razon Social', field: 'numero_folio',thComp: require('../../../globals/th-Filter').default, sortable: true},


                ],
                data: [],
                total: 0,
                query: {
                },
                cargando: false

            }
        },
        mounted() {

        },
        methods: {

        },
        computed: {

        },
        watch: {
     
        },
    }
</script>
