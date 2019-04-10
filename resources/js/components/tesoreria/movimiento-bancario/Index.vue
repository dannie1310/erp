<template>
    <div class="row">
        <div class="col-md-12">
            <movimiento-bancario-create></movimiento-bancario-create>
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
                'include': 'cuenta.empresa,transaccion'
            })
        },

        methods: {
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
            movimientos(){
                return this.$store.getters['tesoreria/movimiento-bancario/movimientos'];
            },
            meta(){
                return this.$store.getters['tesoreria/movimiento-bancario/meta'];
            },
        },
        watch: {
            movimientos: {
                handler(movimientos) {
                    let self = this
                    self.$data.data = []
                    movimientos.forEach(function (movimiento, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: movimiento.numero_folio,
                            fecha: movimiento.fecha,
                            tipo: movimiento.tipo.descripcion,
                            cuenta: movimiento.cuenta ? movimiento.cuenta.numero + ' ' + (movimiento.cuenta.abreviatura ? movimiento.cuenta.abreviatura : '') + ' (' + movimiento.cuenta.empresa.razon_social + ')' : '',
                            referencia: movimiento.transaccion ? movimiento.transaccion.referencia : '',
                            total: '$ ' +  (parseFloat(movimiento.importe) + parseFloat(movimiento.impuesto)).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_movimiento_bancario') ? true : undefined,
                                delete: self.$root.can('eliminar_movimiento_bancario') ? true : undefined,
                                id: movimiento.id,
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
                    this.paginate({...query, include: 'cuenta.empresa,transaccion'})
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