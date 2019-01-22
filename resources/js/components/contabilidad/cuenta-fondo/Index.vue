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
        name: "cuenta-fondo-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Fondo', field: 'id_fondo', sortable: true },
                    { title: 'Saldo', field: 'saldo', sortable: false },
                    { title: 'Editar', field: 'buttons', tdComp: require('./partials/ActionButtons')},
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
                return this.$store.dispatch('contabilidad/cuenta-fondo/paginate', payload)
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-fondo/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-fondo/meta'];
            },
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    cuentas.forEach(function (cuenta, i) {
                        self.$data.data.push({
                            index: cuenta.id,
                            cuenta: cuenta.cuenta,
                            id_fondo: cuenta.fondo.descripcion,
                            saldo:  '$'+parseFloat(cuenta.fondo.saldo).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_fondo') ? true : undefined,
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