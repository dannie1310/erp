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
                            <select class="form-control" v-model="id_tipo_poliza_interfaz">
                                <option value>-- Tipo de Póliza --</option>
                                <option v-for="item in transaccionesInterfaz" v-bind:value="item.id">{{ item.descripcion }}</option>
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
                    { title: 'Concepto', field: 'concepto', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha de Prepóliza', field: 'fecha', sortable: true },
                    { title: 'Total', field: 'total', sortable: true },
                    { title: 'Cuadre', field: 'cuadre'},
                    { title: 'Estatus', field: 'estatus', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Poliza ContPaq', field: 'poliza_contpaq', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    sort: 'fecha',
                    order: 'desc',
                    include: ['relaciones']
                },
                daterange: null,
                id_tipo_poliza_interfaz: '',
                id_estatus: '',
                cargando: false
            }
        },

        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
            this.getEstatus()
            this.getPolizasInterfaz()

            this.id_estatus = this.$router.currentRoute.query.estatus ? this.$router.currentRoute.query.estatus : '';
        },

        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidad/poliza/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidad/poliza/SET_POLIZAS', data.data);
                        this.$store.commit('contabilidad/poliza/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstatus() {
                return this.$store.dispatch('contabilidad/estatus-prepoliza/index')
                    .then(data => {
                        this.$store.commit('contabilidad/estatus-prepoliza/SET_ESTATUS', data.data);
                    })
            },
            getPolizasInterfaz() {
                return this.$store.dispatch('contabilidad/transaccion-interfaz/index', {
                    config: {
                        params: {
                            scope: 'usadas'
                        }
                    }
                })
                    .then(data => {
                        this.$store.commit('contabilidad/transaccion-interfaz/SET_TRANSACCIONES', data.data);
                    })
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
            transaccionesInterfaz() {
                return this.$store.getters['contabilidad/transaccion-interfaz/transacciones']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            polizas: {
                handler(polizas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = polizas.map((poliza, i) => ({
                        index: (i + 1) + self.query.offset,
                        id_tipo_poliza_interfaz: poliza.transaccionInterfaz.descripcion,
                        id_tipo_poliza_contpaq: poliza.tipoPolizaContpaq.descripcion,
                        concepto: poliza.concepto,
                        fecha: poliza.fecha,
                        total: '$' + parseFloat(poliza.total).formatMoney(2, '.', ','),
                        cuadre: '$' + parseFloat(poliza.cuadre).formatMoney(2, '.', ','),
                        estatus: poliza.estatusPrepoliza,
                        poliza_contpaq: poliza.poliza_contpaq ? '# ' + poliza.poliza_contpaq : '',
                        buttons: $.extend({}, {
                            edit: self.$root.can('editar_prepolizas_generadas') ? true : undefined,
                            show: true,
                            historico: false,
                            estatus: (poliza.estatusPrepoliza.estatus != 2 && poliza.estatusPrepoliza.estatus != 3 && poliza.estatusPrepoliza.estatus != 1 && poliza.estatusPrepoliza.estatus != -3) ?  true : undefined,
                            id: poliza.id,
                            id_poliza:poliza.id_poliza,
                            id_empresa:poliza.id_empresa,
                            transaccion: {id:poliza.id, tipo:666},
                        })
                    }));
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
                    this.paginate()
                },
                deep: true
            },
            'daterange.startDate': {
                handler(sd) {
                    this.query.startDate = sd.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.paginate()
                },
                deep: true
            },
            'daterange.endDate': {
                handler(ed) {
                    this.query.endDate = ed.format('YYYY-MM-DD')
                    this.query.offset = 0;
                    this.paginate()
                },
                deep: true
            },
            id_tipo_poliza_interfaz(id_tipo) {
                this.$data.query.id_tipo_poliza_interfaz = id_tipo;
                this.query.offset = 0;
                this.paginate()
            },
            id_estatus(estatus) {
                this.$data.query.estatus = estatus;
                this.query.offset = 0;
                this.paginate()
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        },
    }
</script>
