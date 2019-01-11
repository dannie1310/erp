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
        name: "cuenta-almacen-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Almacén', field: 'id_almacen', sortable: true },
                    { title: 'Tipo de Almacén', field: 'tipo', sortable: false },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },

        mounted() {
            this.fetch()
        },

        methods: {
            fetch(payload = {}) {
                return this.$store.dispatch('contabilidad/cuenta-almacen/paginate', payload)
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-almacen/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-almacen/meta'];
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
                            id_almacen: cuenta.almacen.descripcion,
                            tipo: cuenta.almacen.tipo,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_almacen') ? true : undefined,
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
                    this.fetch(query)
                },
                deep: true
            }
        },
    }
</script>