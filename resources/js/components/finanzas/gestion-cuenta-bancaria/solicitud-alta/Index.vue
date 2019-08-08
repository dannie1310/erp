<template>
    <div class="row">
        <div class="col-12" :disabled="cargando">
            <create @created="paginate()"></create>
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
    import Create from "./Create";
    export default {
        name: "solicitud-alta-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Sucursal', field: 'sucursal', sortable: true },
                    { title: 'Tipo', field: 'tipo', sortable: true},
                    { title: 'Observaciones', field: 'observaciones', sortable: false },
                    { title: 'Estatus', field: 'estado', sortable: true},
                    { title: 'Acciones', field: 'buttons'},
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
                return this.$store.dispatch('finanzas/solicitud-alta-cuenta-bancaria/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_CUENTAS', data.data);
                        this.$store.commit('finanzas/solicitud-alta-cuenta-bancaria/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            cuentas(){
                return this.$store.getters['finanzas/solicitud-alta-cuenta-bancaria/cuentas'];
            },
            meta(){
                return this.$store.getters['finanzas/solicitud-alta-cuenta-bancaria/meta'];
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
                            cuenta: cuenta.cuenta,
                            sucursal: cuenta.sucursal,
                            tipo: cuenta.tipo_cuenta,
                            observaciones: cuenta.observaciones

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
