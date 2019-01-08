<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="form-row">
                        <div class="col">
                            <DateRangePicker class="form-control" placeholder="Rango de Fechas" v-model="$data.daterange"/>
                        </div>
                        <div class="col">
                            <select class="form-control" v-model="id_estatus">
                                <option value>-- Estatus --</option>
                                <option v-for="item in estatus" v-bind:value="item.estatus">{{ item.descripcion }}</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control" v-model="id_tipo_poliza_contpaq">
                                <option value>-- Tipo de Póliza --</option>
                                <option v-for="item in tiposPolizaContaq" v-bind:value="item.id">{{ item.descripcion }}</option>
                            </select>
                        </div>
                    </div>
                </div>
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

    import DateRangePicker from "../../globals/DateRangePicker";
    export default {
        name: "poliza-index",
        components: {DateRangePicker},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Tipo de Póliza', field: 'id_tipo_poliza_interfaz', sortable: true },
                    { title: 'Tipo de Transacción', field: 'id_tipo_poliza_contpaq', sortable: true },
                    { title: 'Concepto', field: 'concepto', thComp: require('../../globals/th-Filter'), sortable: true },
                    { title: 'Fecha', field: 'fecha'},
                    { title: 'Monto', field: 'total'},
                    { title: 'Cuadre', field: 'cuadre'},
                    { title: 'Estado', field: 'estatus', sortable: true, tdComp: require('./partials/EstatusLabel')},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                },
                daterange: null,
                id_tipo_poliza_contpaq: '',
                id_estatus: ''
            }
        },

        mounted() {
            this.fetch()
            this.getEstatus()
            this.getTiposPolizaContaq()
        },

        methods: {
            fetch(payload = {}) {
                return this.$store.dispatch('contabilidad/poliza/paginate', payload)
            },
            getEstatus() {
                return this.$store.dispatch('contabilidad/estatus-prepoliza/fetch')
            },
            getTiposPolizaContaq() {
                return this.$store.dispatch('contabilidad/tipos-poliza-contpaq/fetch')
            }
        },
        computed: {
            polizas(){
                return this.$store.getters['contabilidad/poliza/polizas'];
            },
            meta(){
                return this.$store.getters['contabilidad/poliza/meta'];
            },
            estatus() {
                return this.$store.getters['contabilidad/estatus-prepoliza/estatus']
            },
            tiposPolizaContaq() {
                return this.$store.getters['contabilidad/tipos-poliza-contpaq/tipos']
            }
        },
        watch: {
            polizas: {
                handler(polizas) {
                    let self = this
                    self.$data.data = []
                    polizas.forEach(function (poliza, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
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
                                historico: false,
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
                handler () {
                    this.fetch(this.query)
                },
                deep: true
            },
            'daterange.startDate': {
                handler(sd) {
                    this.$data.query.startDate = sd.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.fetch(this.$data.query)
                },
                deep: true
            },
            'daterange.endDate': {
                handler(ed) {
                    this.$data.query.endDate = ed.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.fetch(this.$data.query)
                },
                deep: true
            },
            id_tipo_poliza_contpaq(id_tipo) {
                this.$data.query.id_tipo_poliza_contpaq = id_tipo;
                this.query.offset = 0;
                this.fetch(this.$data.query)
            },
            id_estatus(estatus) {
                this.$data.query.estatus = estatus;
                this.query.offset = 0;
                this.fetch(this.$data.query)
            }
        },
    }
</script>