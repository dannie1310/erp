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
        name: "cuenta-banco-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Tipo Cuenta Contable', field: 'tipo', sortable: true },
                    { title: 'Tipo Cuenta', field: 'cuenta_contable', sortable: false },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },

        mounted() {
            this.paginate()
        },

        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('contabilidad/cuenta-banco/paginate', payload)
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-banco/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-banco/meta'];
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
                            cuenta: cuenta.cuenta,
                            tipo: cuenta.tipo.descripcion,
                            cuenta_contable: cuenta.cuentaContable.abreviatura,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_banco') ? true : undefined,
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