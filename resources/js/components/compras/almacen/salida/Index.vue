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
        name: "salida-almacen-index",
        components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../../globals/th-Filter')},
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../../globals/th-Date')},
                    { title: 'Referencia', field: 'referencia', sortable: true, thComp: require('../../../globals/th-Filter')},
                    { title: 'Operación', field: 'opciones', sortable: true},
                    { title: 'Almacen', field: 'id_almacen', sortable: true, thComp: require('../../../globals/th-Filter')},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
                ],
                data: [],
                total: 0,
                query: {include: ['almacen'], sort: 'numero_folio',order: 'desc'},
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
                return this.$store.dispatch('compras/salida-almacen/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/salida-almacen/SET_SALIDAS', data.data);
                        this.$store.commit('compras/salida-almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            salidas() {
                return this.$store.getters['compras/salida-almacen/salidas'];
            },
            meta() {
                return this.$store.getters['compras/salida-almacen/meta'];
            },
            tbodyStyle() {
                return this.cargando ? {'-webkit-filter': 'blur(2px)'} : {}
            }
        },
        watch: {
            salidas: {
                handler(salidas) {
                    let self = this
                    self.$data.data = []
                    salidas.forEach(function (salida, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: salida.folio_format,
                            fecha: salida.fecha_format,
                            referencia: salida.referencia,
                            observaciones: salida.observaciones,
                            opciones: salida.operacion,
                            estado: salida.estado_format,
                            id_almacen: salida.almacen.descripcion,
                            buttons: $.extend({}, {
                                show: true,
                                borrar: self.$root.can('eliminar_salida_almacen') ? true : false,
                                id: salida.id

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
