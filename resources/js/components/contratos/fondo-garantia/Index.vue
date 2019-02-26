<template>
    <div class="row">
        <div class="col-md-12">
            <fondo-garantia-create></fondo-garantia-create>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header" style="display: none">

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive table-bordered">
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
    import FondoGarantiaCreate from "./Create";
    export default {
        name: "fondos-garantia-index",
        components: {FondoGarantiaCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', sortable: false },
                    { title: 'Contratista', field: 'empresa__razon_social', thComp: require('../../globals/th-Filter'), sortable: true },
                    { title: 'Referencia', field: 'subcontrato__referencia', thComp: require('../../globals/th-Filter'), sortable: true },
                    { title: 'Folio Subcontrato', field: 'subcontrato__numero_folio', thClass: 'th_folio', thComp: require('../../globals/th-Filter'), sortable: true },
                    { title: 'Fecha Subcontrato', field: 'subcontrato__fecha', thClass: 'th_fecha', sortable: true },
                    { title: 'Monto Subcontrato', field: 'subcontrato__monto', tdClass: 'money', thClass: 'th_money'},
                    { title: 'Saldo Fondo de Garantia', field: 'saldo', sortable: true, tdClass: 'money', thClass: 'th_money'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                }
            }
        },
        mounted() {
            /*this.paginate({
                'include': 'subcontrato.empresa'
            })*/
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
                            empresa__razon_social: fondoGarantia.subcontrato.empresa.razon_social,
                            subcontrato__referencia: fondoGarantia.subcontrato.referencia,
                            subcontrato__numero_folio: fondoGarantia.subcontrato.numero_folio_format,
                            subcontrato__fecha: fondoGarantia.subcontrato.fecha_format,
                            subcontrato__monto: fondoGarantia.subcontrato.monto_format,
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
                    this.paginate({...query, include: 'subcontrato.empresa'})
                },
                deep: true
            }
        },
    }
</script>
<style>
    .money
    {
       text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;

    }
    .th_fecha, .th_folio
    {
        width: 110px;
        max-width: 110px;
        min-width: 110px;

    }
    .th_index
    {
        width: 15px;
        max-width: 20px;
        min-width: 10px;

    }
    th
    {
        text-align: center;
    }
</style>