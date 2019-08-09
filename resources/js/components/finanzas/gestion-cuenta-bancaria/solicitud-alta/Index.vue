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
                    { title: 'Folio', field: 'folio', sortable: false },
                    { title: 'Fecha', field: 'fecha', sortable: false },
                    { title: 'Beneficiario', field: 'empresa', sortable: false},
                    { title: 'Tipo Beneficiaro', field: 'tipo_empresa'},
                    { title: 'Banco', field: 'banco', sortable: false},
                    { title: 'Cuenta/CLABE', field: 'cuenta', sortable: false },
                    { title: 'Estatus', field: 'estatus'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
                ],
                data: [],
                total: 0,
                query: {include: ['moneda', 'subcontrato','empresa','banco','tipo','plaza']},
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
                            folio: cuenta.numero_folio_format_orden,
                            id: cuenta.id,
                            fecha: cuenta.fecha_format,
                            empresa: cuenta.empresa.razon_social,
                            tipo_empresa: cuenta.empresa.tipo_empresa,
                            banco: cuenta.banco.razon_social,
                            cuenta: cuenta.cuenta,
                            estatus: cuenta.estatus,
                            buttons: $.extend({}, {
                                show: true,
                                autorizar: true,//self.$root.can('autorizar_solicitud_alta_cuenta_bancaria_empresa\n') ? true : false,
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
