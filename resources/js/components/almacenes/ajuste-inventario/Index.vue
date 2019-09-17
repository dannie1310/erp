<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_ajuste_positivo')" :disabled="cargando">
            <button @click="create" class="btn btn-app btn-info pull-right">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar Ajuste
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
    import Create from "./Create";
    export default {
        name: "ajuste-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Fecha', field: 'fecha', sortable: true, thComp: require('../../globals/th-Date')},
                    { title: 'Almacén', field: 'id_almacen',sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Referencia', field: 'referencia', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Tipo', field: 'tipo', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Observaciones', field: 'observaciones', sortable: true, thComp: require('../../globals/th-Filter')},
                    { title: 'Estatus', field: 'estado', sortable: true},
                ],
                data: [],
                total: 0,
                query: {sort: 'numero_folio', order: 'desc'},
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
                return this.$store.dispatch('almacenes/ajuste-inventario/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('almacenes/ajuste-inventario/SET_AJUSTES', data.data);
                        this.$store.commit('almacenes/ajuste-inventario/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'ajuste-create'});
            },
        },
        computed: {
            ajustes(){
                return this.$store.getters['almacenes/ajuste-inventario/ajustes'];
            },
            meta(){
                return this.$store.getters['almacenes/ajuste-inventario/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            ajustes: {
                handler(ajustes) {
                    let self = this
                    self.$data.data = []
                    ajustes.forEach(function (ajustes, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            numero_folio: ajustes.numero_folio_format,
                            fecha: ajustes.fecha_format,
                            id_almacen: typeof ajustes.almacen !== 'undefined' ?  ajustes.almacen.descripcion : '',
                            referencia: ajustes.referencia,
                            tipo: ajustes.tipo,
                            observaciones: ajustes.observaciones,
                            estado: ajustes.estado_format
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
