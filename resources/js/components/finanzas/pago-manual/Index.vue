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
        name: "pago-manual-index",
        components: {},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Empresa', field: 'razon_social', sortable:true},
                    { title: 'Fecha', field:'fecha', sortable:true},
                    { title: 'Moneda',field: 'moneda', tdClass: 'td_c100', thClass: 'th_c100', sortable: true},
                    { title: 'Cheque',field: 'referencia', tdClass: 'td_c120', thClass: 'th_c120', sortable: true},
                    { title: 'Saldo',field: 'saldo', tdClass: 'td_money', thClass: 'th_money', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: ['moneda', 'empresa'], scope:'pendientePorAplicar', sort: 'id_transaccion',  order: 'desc'

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
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('finanzas/pago/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('finanzas/pago/SET_PAGOS', data.data);
                        this.$store.commit('finanzas/pago/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            pagos(){
              return this.$store.getters['finanzas/pago/pagos'];
            },
            meta(){
              return this.$store.getters['finanzas/pago/meta']
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
                            numero_folio: pago.numero_folio_format,
                            razon_social: pago.empresa.razon_social,
                            fecha: pago.fecha_format,
                            moneda: pago.moneda.nombre,
                            referencia: pago.referencia,
                            saldo: pago.saldo_format,
                            buttons: $.extend({}, {
                                aplicar: true,
                                id: pago.id,
                                id_empresa: pago.id_empresa,
                                pago: {
                                    numero_folio: pago.numero_folio_format,
                                    razon_social: pago.empresa.razon_social,
                                    referencia: pago.referencia,
                                }
                            })
                        })

                    });

                },
                deep: true
            },
            meta: {
                handler(meta) {
                    let total = meta.pagination.total
                    this.$data.total = total
                },
                deep: true
            },
            query: {
                handler(query) {
                    this.paginate(query)
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
