<template>
    <div class="row">
        <div class="col-12">
          <cuenta-banco-create @created="paginate(query)"></cuenta-banco-create>
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
    import CuentaBancoCreate from "./Create";
    export default {
        name: "cuenta-banco-index",
        components: {CuentaBancoCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'razon_social', sortable: true },
                    { title: 'Número de Cuentas Registradas', field: 'cuentas_count', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {}
            }
        },
        mounted() {
            this.paginate()
        },
        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('cadeco/cuenta/paginate', {
                    ...payload,
                    include: ['empresa','cuentasBanco'],
                    scope: 'paraTraspaso'
                })
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['cadeco/cuenta/cuentas'];
            },
            meta(){
                return this.$store.getters['cadeco/cuenta/meta'];
            },
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    cuentas.forEach(function (cuenta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            razon_social: cuenta.numero+" ("+cuenta.abreviatura+" "+cuenta.empresa.razon_social+")",
                            cuentas_count: cuenta.cuentasBanco.data.length,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_cuenta_banco') ? true : undefined,
                                razon_social: cuenta.numero+" ("+cuenta.abreviatura+" "+cuenta.empresa.razon_social+")",
                                id: cuenta.id
                            })
                        })
                    });
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
            query: {
                handler (query) {
                    this.paginate(query)
                },
                deep: true
            }
        },
    }
</script>