<template>
    <div class="row">
        <div class="col-md-12">

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

    export default {
        name: "fondos-garantia-index",

        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },

                    { title: 'Contratista', field: 'contratista', sortable: true },
                    { title: 'Folio Subcontrato', field: 'numero_folio_subcontrato', sortable: true },
                    { title: 'Fecha Subcontrato', field: 'fecha_subcontrato', sortable: true },
                    { title: 'Monto Subcontrato', field: 'monto_subcontrato', align: 'right'},
                    { title: 'Saldo Fondo de Garantia', field: 'saldo', sortable: true, align: 'right'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },
        mounted() {
            /*this.paginate()*/
        },
        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('contratos/fondo-garantia/paginate', payload)
            }
        },
        computed: {
            fondosGarantia(){
                return this.$store.getters['contratos/fondo-garantia/fondosGarantia'];
            },
            meta(){
                return this.$store.getters['contratos/fondo-garantia/meta'];
            },
        },
        watch: {
            fondosGarantia: {
                handler(fondosGarantia) {
                    let self = this
                    self.$data.data = []
                    fondosGarantia.forEach(function (fondoGarantia, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,

                            saldo: fondoGarantia.saldo,
                            contratista: fondoGarantia.contratista,
                            numero_folio_subcontrato: fondoGarantia.numero_folio_subcontrato,
                            fecha_subcontrato: fondoGarantia.fecha_subcontrato,
                            monto_subcontrato: fondoGarantia.monto_subcontrato,


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
                    this.paginate()
                },
                deep: true
            }
        },
    }
</script>