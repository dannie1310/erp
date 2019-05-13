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
                    { title: '# Folio', field: 'numero_folio', sortable: true },
                    { title: 'Tipo de Solicitud', field: 'tipo_solicitud', sortable: false },
                    { title: 'Rubro', field: 'tipo_solicitud', sortable: false },
                    { title: 'Transacción Antecedente', field: 'antecedente', sortable: false },
                    { title: 'Monto', field: 'monto', sortable: false },
                    { title: 'Beneficiario', field: 'beneficiario', sortable: false },
                    { title: 'Fecha y Hora de Registro', field: 'fecha_registro', sortable: false },
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
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