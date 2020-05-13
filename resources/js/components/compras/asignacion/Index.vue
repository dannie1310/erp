<template>
    <div class="row">
        <div class="col-12">
            <!-- v-if="$root.can('registrar_requisicion_compra')" -->
            <button @click="create" class="btn btn-app btn-default pull-right" v-if="$root.can('registrar_asignacion_proveedor')" :disabled="cargando">
                <i class="fa fa-plus"></i> Registrar
            </button>
            <!-- <Layout @change="paginate()"></Layout> -->
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
    import Layout from "./CargaLayout";
    export default {
        name: "asignacion-proveedores-index",
        components: {Layout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio Solicitud', field: 'id_transaccion_solicitud',sortable: true},
                    { title: 'Folio Asignación', field: 'id',sortable: true},
                    { title: 'Concepto', field: 'observaciones'},
                    { title: 'Fecha/Hora', field: 'registro',sortable: true},
                    { title: 'Estado', field: 'estado', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'id', order: 'desc'},
                estado: "",
                cargando: false
            }
        },

        mounted() {
            this.cargando = true;
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                    this.cargando = false;
                })
        },

        methods: {
            create(){
                this.$router.push({name: 'asignacion-proveedores-create'});
            },
            paginate() {
                return this.$store.dispatch('compras/asignacion/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('compras/asignacion/SET_ASIGNACIONES', data.data);
                        this.$store.commit('compras/asignacion/SET_META', data.meta);
                    })
            }
        },
        computed: {
            asignaciones(){
                return this.$store.getters['compras/asignacion/asignaciones'];
            },
            meta(){
                return this.$store.getters['compras/asignacion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            asignaciones: {
                handler(asignaciones) {
                    let self = this
                    self.$data.data = []
                    asignaciones.forEach(function (asignacion, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id_transaccion_solicitud: asignacion.folio_solicitud_format,
                            id: asignacion.folio_asignacion_format,
                            observaciones: asignacion.observaciones,
                            estado: asignacion.estado,
                            registro: asignacion.fecha_format,
                            buttons: $.extend({}, {
                                // id:inventario.id,
                                // marbete: self.$root.can('generar_marbetes'),
                                // layout: self.$root.can('descarga_layout_captura_conteos'),
                                // resumen: self.$root.can('descargar_resumen_conteos'),
                                // estado: inventario.estado,
                                // actualizar: self.$root.can('cerrar_inventario_fisico')
                                id: asignacion.id
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
