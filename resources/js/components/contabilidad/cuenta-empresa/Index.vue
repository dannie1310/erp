<template>
    <div class="row">
        <div class="col-12">
            <cuenta-empresa-create @created="paginate(query)"></cuenta-empresa-create>
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
    import CuentaEmpresaCreate from "./Create";
    export default {
        name: "cuenta-empresa-index",
        components: {CuentaEmpresaCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Empresa', field: 'razon_social', sortable: true },
                    { title: 'Cuentas Registradas', field: 'cuentas_count', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {}
            }
        },

        mounted() {
            this.paginate()
            this.$store.dispatch('contabilidad/tipo-cuenta-empresa/index')
        },

        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('cadeco/empresa/paginate', {
                    ...payload,
                    include: 'cuentasEmpresa',
                    scope: 'conCuentas'
                })
            }
        },
        computed: {
            empresas(){
                return this.$store.getters['cadeco/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['cadeco/empresa/meta'];
            },
        },
        watch: {
            empresas: {
                handler(empresas) {
                    let self = this
                    self.$data.data = []
                    empresas.forEach(function (empresa, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            razon_social: empresa.razon_social,
                            cuentas_count: empresa.cuentasEmpresa.data.length,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_cuenta_empresa') ? true : undefined,
                                id: empresa.id
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
