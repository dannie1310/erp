<template>
    <div class="row">
        <div class="col-12">
            <router-link :to="{name: 'estimacion-proveedor-seleccionar-subcontrato'}" v-if="$root.can('registrar_estimacion_proveedor')" class="btn btn-app btn-info float-right" :disabled="cargando">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
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
        name: "estimacion-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', tdClass: 'folio', sortable: false},
                    { title: 'Fecha', field: 'fecha', sortable: false},
                    { title: 'Subcontrato', tdClass: 'folio', field: 'numero_folio_sub'},
                    { title: 'Referencia Subcontrato', field: 'referencia_sub', sortable: false},
                    { title: 'Contratista', field: 'contratista', sortable: false},
                    { title: 'Monto', field: 'monto', tdClass: ['th_money', 'text-right'], sortable: false},
                    { title: 'Proyecto', field: 'proyecto', sortable: false},
                    { title: 'Estado', field: 'estado', sortable: false, tdClass: 'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                cargando: false
            }
        },
        mounted() {
            this.query.include = ['subcontrato.empresa'];
            this.query.sort = 'numero_folio';
            this.query.order = 'DESC'

            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contratos/estimacion/indexProveedor', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contratos/estimacion/SET_ESTIMACIONES', data.data);
                        this.$store.commit('contratos/estimacion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            getEstado(estado) {
                let val = parseInt(estado);
                switch (val) {
                    case 0:
                        return {
                            color: '#f39c12',
                            descripcion: 'Registrada'
                        }
                    case 1:
                        return {
                            color: '#0073b7',
                            descripcion: 'Aprobada'
                        }
                    case 2:
                        return {
                            color: '#00a65a',
                            descripcion: 'Revisada'
                        }
                    default:
                        return {
                            color: '#d2d6de',
                            descripcion: 'Desconocido'
                        }
                }
            }
        },
        computed: {
            estimaciones(){
                return this.$store.getters['contratos/estimacion/estimaciones'];
            },
            meta(){
                return this.$store.getters['contratos/estimacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            estimaciones: {
                handler(estimaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = estimaciones.map((estimacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: estimacion.numero_folio_format,
                        numero_folio_sub: estimacion.numero_folio_sub,
                        referencia_sub: estimacion.referencia_sub,
                        contratista: estimacion.contratista,
                        fecha: estimacion.fecha,
                        proyecto: estimacion.proyecto,
                        estado: this.getEstado(estimacion.estado),
                        monto: estimacion.monto_format,
                        buttons: $.extend({}, {
                            id: estimacion.id,
                            show: self.$root.can('consultar_estimacion_proveedor') ? true : true,
                            edit: self.$root.can('consultar_estimacion_proveedor') ? true : true,
                            base: estimacion.base
                        })

                    }));
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
                    this.paginate()
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
