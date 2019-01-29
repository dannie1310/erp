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
        name: "movimiento-bancario-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true },
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Tipo', field: 'tipo', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: false },
                    { title: 'Referencia', field: 'referencia'},
                    { title: 'Total', field: 'total'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },

        mounted() {
            this.paginate({
                'include': 'cuenta.empresa,transaccion'
            })
        },

        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('tesoreria/movimiento-bancario/paginate', payload)
            }
        },
        computed: {
            movimientos(){
                return this.$store.getters['tesoreria/movimiento-bancario/movimientos'];
            },
            meta(){
                return this.$store.getters['tesoreria/movimiento-bancario/meta'];
            },
        },
        watch: {
            movimientos: {
                handler(movimientos) {
                    let self = this
                    self.$data.data = []
                    movimientos.forEach(function (movimiento, i) {
                        self.$data.data.push({
                            index: movimiento.id,
                            numero_folio: movimiento.numero_folio,
                            fecha: movimiento.fecha,
                            tipo: movimiento.tipo.descripcion,
                            cuenta: movimiento.cuenta ? movimiento.cuenta.numero + ' ' + movimiento.cuenta.abreviatura + ' (' + movimiento.cuenta.empresa.razon_social + ')' : '',
                            referencia: movimiento.transaccion ? movimiento.transaccion.referencia : '',
                            total: '$ ' +  (parseFloat(movimiento.importe) + parseFloat(movimiento.impuesto)).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_movimiento_bancario') ? true : undefined,
                                delete: self.$root.can('eliminar_movimiento_bancario') ? true : undefined,
                                id: movimiento.id,
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
                    this.paginate({...query, include: 'cuenta.empresa,transaccion'})
                },
                deep: true
            }
        },
    }
</script>