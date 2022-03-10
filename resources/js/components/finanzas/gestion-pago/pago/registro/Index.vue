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
        name: "registro-pago-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: false},
                    { title: 'Fecha', field: 'fecha', sortable: false},
                    { title: 'Beneficiario', field: 'empresa',sortable: false},
                    { title: 'Tipo', field: 'tipo', sortable: false},
                    { title: 'Monto', field: 'monto', thClass: 'th_money', tdClass: 'td_money', sortable: false},
                    { title: 'Moneda', field: 'id_moneda', sortable: false },
                    { title: 'Estado', field: 'estado',  sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort: 'id_transaccion', order: 'desc'},
                estado: "",
                cargando: false,
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
                return this.$store.dispatch('finanzas/pago/documentosParaPagar', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/pago/SET_PAGOS', data);
                        this.total = data.length
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            pagos(){
                return this.$store.getters['finanzas/pago/pagos'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            pagos: {
                handler(pagos) {
                    let self = this
                    self.$data.data = []
                    pagos.forEach(function (pago, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: pago.numero_folio,
                            fecha: pago.fecha,
                            empresa: pago.empresa,
                            tipo: pago.tipo,
                            monto: pago.monto_format,
                            estado: pago.estado_format,
                            id_moneda:pago.moneda,
                            buttons: $.extend({}, {
                                id: pago.id,
                            })
                        })
                    });
                },
                deep: false
            },
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>
