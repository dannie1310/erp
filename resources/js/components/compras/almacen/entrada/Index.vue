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
        name: "entrada-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'folio', sortable: false },
                    { title: 'Fecha', field: 'fecha', sortable: false },
                    { title: 'Empresa', field: 'empresa', sortable: false},
                    { title: 'Referencia', field: 'referencia'},
                    { title: 'Observaciones', field: 'observaciones', sortable: false},
                    { title: 'Estatus', field: 'estado'},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
                ],
                data: [],
                total: 0,
                query: { include: 'empresa', sort: 'numero_folio', order: 'desc'},
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
                return this.$store.dispatch('compras/entrada-almacen/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/entrada-almacen/SET_ENTRADAS', data.data);
                        this.$store.commit('compras/entrada-almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            entradas(){
                return this.$store.getters['compras/entrada-almacen/entradas'];
            },
            meta(){
                return this.$store.getters['compras/entrada-almacen/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            entradas: {
                handler(entradas) {
                    let self = this
                    self.$data.data = []
                    entradas.forEach(function (entrada, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            folio: entrada.numero_folio_format,
                            fecha: entrada.fecha_registro,
                            empresa: entrada.empresa.razon_social,
                            referencia: entrada.referencia,
                            observaciones: entrada.observaciones,
                            estado: entrada.estado_format,
                            buttons: $.extend({}, {
                                id: entrada.id,
                                estado: entrada.estado,
                                delete: self.$root.can('eliminar_entrada_almacen') ? true : false,
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
