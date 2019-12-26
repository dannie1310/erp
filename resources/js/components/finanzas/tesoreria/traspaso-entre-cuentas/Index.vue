<template>
    <div class="row">
        <div class="col-md-12">
            <traspaso-entre-cuentas-create @created="created($event)" v-if="$root.can('registrar_traspaso_cuenta')"></traspaso-entre-cuentas-create>
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
    import TraspasoEntreCuentasCreate from "./Create";
    export default {
        name: "traspaso-entre-cuentas-index",
        components: {TraspasoEntreCuentasCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio', sortable: true },
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Cuenta Origen', field: 'cuenta_origen', sortable: false },
                    { title: 'Cuenta Destino', field: 'cuenta_destino', sortable: false },
                    { title: 'Importe', field: 'importe', sortable: true},
                    { title: 'Referencia', field: 'referencia'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: { include: ['cuentaOrigen.empresa','cuentaDestino.empresa'], sort: 'numero_folio', order: 'DESC'},
                search: '',
                cargando: false
            }
        },

        mounted() {
            this.paginate(this.query)
        },

        methods: {
            created() {
                this.paginate(this.query)
            },

            paginate(payload = {}) {
                this.cargando = true;
                return this.$store.dispatch('finanzas/traspaso-entre-cuentas/paginate', {params: payload})
                    .then(data => {
                        this.$store.commit('finanzas/traspaso-entre-cuentas/SET_TRASPASOS', data.data);
                        this.$store.commit('finanzas/traspaso-entre-cuentas/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            traspasos(){
                return this.$store.getters['finanzas/traspaso-entre-cuentas/traspasos'];
            },
            meta(){
                return this.$store.getters['finanzas/traspaso-entre-cuentas/meta'];
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
                            cuenta_origen: traspaso.cuentaOrigen ? traspaso.cuentaOrigen.numero + ' ' + (traspaso.cuentaOrigen.abreviatura ? traspaso.cuentaOrigen.abreviatura : '') + (traspaso.cuentaOrigen.empresa ? ' (' + traspaso.cuentaOrigen.empresa.razon_social + ')' : '') : '',
                            cuenta_destino: traspaso.cuentaDestino ? traspaso.cuentaDestino.numero + ' ' + (traspaso.cuentaDestino.abreviatura ? traspaso.cuentaDestino.abreviatura : '') + (traspaso.cuentaDestino.empresa ? ' (' + traspaso.cuentaDestino.empresa.razon_social + ')' : '') : '',
                            referencia: traspaso.traspasoTransaccion.debito ? traspaso.traspasoTransaccion.debito.referencia : traspaso.traspasoTransaccion.credito.referencia,
                            importe: '$ ' + parseFloat(traspaso.importe).formatMoney(2, '.', ','),
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_traspaso_cuenta') ? true : undefined,
                                delete: self.$root.can('eliminar_traspaso_cuenta') ? true : undefined,
                                id: traspaso.id_traspaso,
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
                    this.paginate(this.query)
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