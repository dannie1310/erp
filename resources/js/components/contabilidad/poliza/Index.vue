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
        name: "poliza-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Tipo de Póliza', field: 'id_tipo_poliza_interfaz', sortable: true },
                    { title: 'Tipo de Transacción', field: 'id_tipo_poliza_contpaq', sortable: true },
                    { title: 'Concepto', field: 'concepto', sortable: false },
                    { title: 'Fecha', field: 'fecha'},
                    { title: 'Monto', field: 'total'},
                    { title: 'Cuadre', field: 'cuadre'},
                    { title: 'Estado', field: 'estatus', tdComp: require('./partials/EstatusLabel')},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
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
                return this.$store.dispatch('contabilidad/poliza/fetch', payload)
            }
        },
        computed: {
            polizas(){
                return this.$store.getters['contabilidad/poliza/polizas'];
            },
            meta(){
                return this.$store.getters['contabilidad/poliza/meta'];
            },
        },
        watch: {
            polizas: {
                handler(polizas) {
                    let self = this
                    self.$data.data = []
                    polizas.forEach(function (poliza, i) {
                        self.$data.data.push({
                            index: poliza.id,
                            id_tipo_poliza_interfaz: poliza.transaccionInterfaz.data.descripcion,
                            id_tipo_poliza_contpaq: poliza.tipoPolizaContpaq.data.descripcion,
                            concepto: poliza.concepto,
                            fecha: poliza.fecha,
                            total: '$'+parseFloat(poliza.total).formatMoney(2, '.', ','),
                            cuadre: '$'+parseFloat(poliza.cuadre).formatMoney(2, '.', ','),
                            estatus: poliza.estatusPrepoliza.data,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_prepolizas_generadas') ? true : undefined,
                                show: true,
                                historico: poliza.tiene_historico,
                                id: poliza.id
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