<template>
    <div class="row">
        <div class="col-12">
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
        name: "venta-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'folio', tdClass: 'td_money',sortable: true},
                    { title: 'Empresa', field: 'fecha_format', tdClass: 'td_money',sortable: true},
                    { title: 'Concepto', field: 'estado', sortable: true},
                    { title: 'Estatus', field: 'cotizacion'},
                    { title: 'Acciones', field: 'observaciones'},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
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
                return this.$store.dispatch('ventas/venta/paginate', { params: this.query})
                    .then(data => {
                        console.log(data);
                        this.$store.commit('ventas/venta/SET_VENTAS', data.data);
                        this.$store.commit('ventas/venta/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },
        computed: {
            ventas(){
                return this.$store.getters['ventas/venta/ventas'];
            },
            meta(){
                return this.$store.getters['ventas/venta/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            ventas: {
                handler(ventas) {
                    let self = this
                    self.$data.data = []
                    ventas.forEach(function (venta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            // folio: asignacion.folio_format,
                            // id_tipo: asignacion.id_tipo,
                            // fecha_format: asignacion.fecha_format,
                            // observaciones: asignacion.observaciones,
                            // buttons: $.extend({}, {
                            //     id:inventario.id,
                            //     marbete: self.$root.can('generar_marbetes'),
                            //     layout: self.$root.can('descarga_layout_captura_conteos'),
                            //     resumen: self.$root.can('descargar_resumen_conteos'),
                            //     estado: inventario.estado,
                            //     actualizar: self.$root.can('cerrar_inventario_fisico')
                            // })
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
