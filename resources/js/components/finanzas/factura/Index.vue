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
        name: "factura-index",
        components: {},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Folio', field: 'id_transaccion',thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Folio Contrarecibo', field: 'numero_folio',thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Referencia', field: 'referencia',thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Empresa', field: 'id_empresa',thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date'), sortable: true},
                    { title: 'Importe', field: 'monto', sortable: true},
                    { title: 'Saldo', field: 'saldo', sortable: true},
                    { title: 'Estado', field: 'estado',thComp: require('../../globals/th-Filter'), sortable: true},
                    { title: 'Observaciones Contrarecibo', field: 'observaciones',thComp: require('../../globals/th-Filter'), sortable: false},


                ],
                data: [],
                total: 0,
                query: {
                    include: ['contra_recibo','empresa'], sort: 'id_transaccion',  order: 'desc'
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
                return this.$store.dispatch('finanzas/factura/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('finanzas/factura/SET_FACTURAS', data.data);
                        this.$store.commit('finanzas/factura/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            facturas(){
                return this.$store.getters['finanzas/factura/facturas'];
            },
            meta(){
                return this.$store.getters['finanzas/factura/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            facturas: {
                handler(facturas) {
                    let self = this
                    self.$data.data = []
                    facturas.forEach(function (factura, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id_transaccion: '# ' + factura.numero_folio,
                            numero_folio: '# ' + factura.contra_recibo.numero_folio,
                            referencia: factura.referencia,
                            id_empresa: factura.empresa.razon_social,
                            monto: factura.monto_format,
                            saldo: factura.saldo_format,
                            fecha: factura.fecha_format,
                            estado: factura.estado_format,
                            observaciones: factura.contra_recibo.observaciones

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
