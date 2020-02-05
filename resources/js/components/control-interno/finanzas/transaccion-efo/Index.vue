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
        name: "transaccion-efos-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Transacción', field: 'tipo_transaccion', sortable: false},
                    { title: 'RFC', field: 'rfc', sortable: false},
                    { title: 'Razón Social', field: 'razon_social', sortable: false},
                    { title: 'Fecha', field: 'fecha', sortable: false},
                    { title: 'Referencia', field: 'referencia', thClass: 'col-6', sortable: false}                    
                ],
                data: [],
                total: 0,
                query: {},
                estado: "",
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
                return this.$store.dispatch('seguridad/finanzas/transaccion-efo/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('seguridad/finanzas/transaccion-efo/SET_TRANSACCIONES', data.data);
                        this.$store.commit('seguridad/finanzas/transaccion-efo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            transacciones(){
                return this.$store.getters['seguridad/finanzas/transaccion-efo/transacciones'];
            },
            meta(){
                return this.$store.getters['seguridad/finanzas/transaccion-efo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            transacciones: {
                handler(famls) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = famls.map((transaccion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: transaccion.numero_folio,
                        tipo_transaccion: transaccion.tipo_transaccion,
                        rfc: transaccion.efo.rfc,
                        razon_social: transaccion.razon_social,
                        referencia: transaccion.referencia,
                        fecha: transaccion.fecha 
                    }));
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
        }
    }
</script>
<style>
    .money
    {
        text-align: right;
    }
</style>
