<template>
    <div class="row">
        <div class="col-12">
            <button @click="create" v-if="$root.can('registrar_comprobante_fondo')" class="btn btn-app pull-right">
                <i class="fa fa-plus"></i> Registrar
            </button>
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
                query: {include: [], sort: 'folio', order: 'desc'},
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
                return this.$store.dispatch('controlRecursos/relacion-gasto/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('controlRecursos/relacion-gasto/SET_RELACIONES', data.data);
                        this.$store.commit('controlRecursos/relacion-gasto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'relacion-gasto-create'});
            },
        },
        computed: {
            relaciones(){
                return this.$store.getters['controlRecursos/relacion-gasto/relaciones'];
            },
            meta(){
                return this.$store.getters['finanzas/cuenta-bancaria-empresa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            relaciones: {
                handler(relaciones) {
                    let self = this
                    self.$data.data = []
                    relaciones.forEach(function (relacion, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            Fecha: documento.fecha_format,
                            IdProveedor: documento.proveedor_descripcion,
                            concepto: documento.concepto,
                            foliodocto: documento.folio_format,
                            total: documento.total_format,
                            idmoneda: documento.moneda,
                            idserie: documento.serie,
                            idtipodocto: documento.tipo_documento,
                            /*buttons: $.extend({}, {
                                show: true,
                                id: cuenta.id,
                                estado: cuenta.estado
                            })*/

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
