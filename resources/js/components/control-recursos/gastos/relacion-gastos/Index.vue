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
        name: "relacion-gasto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Beneficiario', field: 'id_empresa', thComp: require('../../../globals/th-Filter'), sortable: true},
                    { title: 'Tipo Beneficiaro', field: 'empresa__tipo_empresa', sortable: true},
                    { title: 'Banco', field: 'id_banco', thComp: require('../../../globals/th-Filter'), sortable: true},
                    { title: 'Cuenta/CLABE', field: 'cuenta_clabe', thComp: require('../../../globals/th-Filter'), sortable: true},
                    { title: 'Estatus', field: 'estatus', sortable: true},
                   //{ title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {include: ['empresa', 'tipo','banco'], sort: 'id', order: 'desc'},
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
               /* this.cargando = true;
                return this.$store.dispatch('finanzas/cuenta-bancaria-empresa/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/cuenta-bancaria-empresa/SET_CUENTAS', data.data);
                        this.$store.commit('finanzas/cuenta-bancaria-empresa/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })*/
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['finanzas/cuenta-bancaria-empresa/cuentas'];
            },
            meta(){
                return this.$store.getters['finanzas/cuenta-bancaria-empresa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    cuentas.forEach(function (cuenta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id_empresa: cuenta.empresa.razon_social,
                            empresa__tipo_empresa: cuenta.empresa.tipo,
                            id_banco: cuenta.banco.razon_social,
                            cuenta_clabe: cuenta.cuenta,
                            estatus: cuenta.estado_format,
                            buttons: $.extend({}, {
                                show: true,
                                id: cuenta.id,
                                estado: cuenta.estado
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
        }
    }
</script>
<style>
    .money
    {
        text-align: right;
    }
</style>
