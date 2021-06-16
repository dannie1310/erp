<template>
    <div class="row">
        <div class="col-12">
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
        name: "asociar-poliza-index",
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
                    //{ title: 'Estatus', field: 'estatus', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Poliza ContPaq', field: 'poliza_contpaq', sortable: true },
                    //{ title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    sort: 'fecha',
                    order: 'desc',
                    scope: 'getAsociarCFDI'
                },
                cargando: false
            }
        },

        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
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
        },
        computed: {
            polizas(){
                return this.$store.getters['contabilidad/poliza/polizas'];
            },
            meta(){
                return this.$store.getters['contabilidad/poliza/meta'];
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
                        estatus: '',
                        poliza_contpaq: poliza.poliza_contpaq ? '# ' + poliza.poliza_contpaq : '',
                        buttons: $.extend({}, {
                            id: poliza.id,
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
            search(val) {
                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }
                this.timer = setTimeout(() => {
                    this.query.search = val;
                    this.query.offset = 0;
                    this.paginate();

                }, 500);
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
