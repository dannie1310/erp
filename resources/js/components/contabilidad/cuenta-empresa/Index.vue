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
        name: "cuenta-empresa-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Empresa', field: 'empresa', sortable: true },
                    { title: 'Tipo de Cuenta', field: 'tipo', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
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
                return this.$store.dispatch('contabilidad/cuenta-empresa/paginate', payload)
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-empresa/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-empresa/meta'];
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
                            empresa: cuenta.empresa.razon_social,
                            tipo: cuenta.tipo.descripcion,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_empresa') ? true : undefined,
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
