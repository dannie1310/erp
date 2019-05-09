<template>
    <div class="row">
        <div class="col-12">
            <create></create>
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
    import Create from "./Create";
    export default {
        name: "solicitud-pago-anticipado-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'id', sortable: false },
                ],
                data: [],
                total: 0,
                query: {},
                estado: "",
                cargando: false
            }
        },

        mounted() {

        },

        methods: {

        },
        computed: {

        },
    }
</script>