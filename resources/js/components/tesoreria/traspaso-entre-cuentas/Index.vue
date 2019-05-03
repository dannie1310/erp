<template>
    <div class="row">
        <div class="col-md-12">
            <movimiento-bancario-create @created="created()" v-if="$root.can('registrar_movimiento_bancario')"></movimiento-bancario-create>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
                            </div>
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
    import MovimientoBancarioCreate from "./Create";
    export default {
        name: "movimiento-bancario-index",
        components: {MovimientoBancarioCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true },
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Tipo', field: 'tipo', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: false },
                    { title: 'Referencia', field: 'referencia'},
                    { title: 'Importe', field: 'importe'},
                    { title: 'Impuesto', field: 'impuesto'},
                    { title: 'Total', field: 'total'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false
            }
        },

        mounted() {
            this.paginate({
                'include': 'cuenta.empresa,transaccion',
                'sort': 'numero_folio',
                'order': 'DESC'
            })
        },

        methods: {
            created() {
                this.paginate({
                    'include': 'cuenta.empresa,transaccion',
                    'sort': 'numero_folio',
                    'order': 'DESC'
                })
            },

            paginate(payload = {}) {
                this.cargando = true;
                return this.$store.dispatch('tesoreria/movimiento-bancario/paginate', {params: payload})
                    .then(data => {
                        this.$store.commit('tesoreria/movimiento-bancario/SET_MOVIMIENTOS', data.data);
                        this.$store.commit('tesoreria/movimiento-bancario/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            traspasos(){
                return this.$store.getters['tesoreria/traspaso-entra-cuentas/traspasos'];
            },
            meta(){
                return this.$store.getters['tesoreria/traspaso-entra-cuentas/meta'];
            },
        },
        watch: {
            traspasos: {
                handler(traspasos) {
                    let self = this
                    self.$data.data = []
                    traspasos.forEach(function (traspaso, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: traspaso.numero_folio,
                            fecha: traspaso.fecha,
                            cuenta_origen: traspaso.cuenta_origen ? traspaso.cuenta_origen.numero + ' ' + (traspaso.cuenta_origen.abreviatura ? traspaso.cuenta_origen.abreviatura : '') + ' (' + traspaso.cuenta_origen.empresa.razon_social + ')' : '',
                            cuenta_destino: traspaso.cuenta_destino ? traspaso.cuenta_destino.numero + ' ' + (traspaso.cuenta_destino.abreviatura ? traspaso.cuenta_destino.abreviatura : '') + ' (' + traspaso.cuenta_destino.empresa.razon_social + ')' : '',
                            referencia: traspaso.transaccion ? traspaso.transaccion.referencia : '',
                            importe: '$ ' + parseFloat(traspaso.importe).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_traspaso_cuenta') ? true : undefined,
                                delete: self.$root.can('eliminar_traspaso_cuenta') ? true : undefined,
                                id: traspaso.id,
                            })
                        })
                    });
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
                handler (query) {
                    this.paginate({...query,
                        include: 'cuenta.empresa,transaccion',
                        'sort': 'numero_folio',
                        'order': 'DESC'
                    })
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
                    this.paginate({...this.query, include: 'cuenta.empresa,transaccion'})
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