<template>
<span>
    <create @created="paginate()"></create>
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
</span>
</template>
<script>
    import Create from "./Create";
    export default {
        name: "factura-index",
        components: {Create},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Tipo', field: 'opciones',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Folio Contrarecibo', field: 'numero_folio',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Folio Factura', field: 'id_transaccion',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Referencia Factura', field: 'referencia',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Empresa', field: 'id_empresa',thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha Factura', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true},
                    { title: 'Importe Factura', field: 'monto', tdClass: 'money', thClass: 'th_money', sortable: true},
                    { title: 'A Cuenta Factura', field: 'a_cuenta', tdClass: 'money', thClass: 'th_money', sortable: false},
                    { title: 'Saldo Factura', field: 'saldo', tdClass: 'money', thClass: 'th_money', sortable: true},
                    { title: 'Estado Factura', field: 'estado', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Observaciones Contrarecibo', field: 'observaciones',thComp: require('../../globals/th-Filter').default, sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
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

            },
            create() {
                this.$router.push({name: 'factura-create'});
            },
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
                            a_cuenta : factura.a_cuenta,
                            saldo: factura.saldo_format,
                            fecha: factura.fecha_format,
                            estado: factura.estado_format,
                            opciones: factura.opciones_format,
                            observaciones: factura.contra_recibo.observaciones,
                            buttons: $.extend({}, {
                                id: factura.id,
                                show: self.$root.can('consultar_factura') ? true : false,
                                factura: factura,
                                borrar: self.$root.can('eliminar_factura') && factura.estado === 0 ? true : false,
                                revisar: self.$root.can('revisar_factura') && factura.estado === 0 ? true : false,
                                revisar_varios: self.$root.can('registrar_factura_varios') && factura.estado === 0 ? true : false,
                                revertir: self.$root.can('revertir_revision_factura') && factura.estado === 1 ? true : false,
                                transaccion: {id:factura.id, tipo:65},
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
