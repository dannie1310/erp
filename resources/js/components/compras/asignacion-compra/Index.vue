<template>
    <div class="row">
        <div class="col-12"  v-if="!$root.can('registrar_asignacion_compra')" :disabled="cargando">
            <button  @click="create" title="Crear" class="btn btn-app btn-info pull-right" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar Asignación
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
        name: "asignacion-compra-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Número de Folio', field: 'numero_folio_solicitud', sortable: true },
                    { title: 'Fecha', field: 'fecha', sortable: true },
                    { title: 'Observaciones', field: 'observaciones', sortable: true },
                    { title: 'Registró', field: 'registro', sortable: false },
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},


                ],
                data: [],
                total: 0,
                query: {},
                search: '',
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
                return this.$store.dispatch('compras/asignacion-compra/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/asignacion-compra/SET_ASIGNACIONES', data.data);
                        this.$store.commit('compras/asignacion-compra/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create() {
                this.$router.push({name: 'asignacion-compra-create'});
            },
        },
        computed: {
            asignaciones(){
                return this.$store.getters['compras/asignacion-compra/asignaciones'];
            },

            meta(){
                return this.$store.getters['compras/asignacion-compra/meta'];
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
                    self.$data.data = asignaciones.map((asignacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio_solicitud: `# ${asignacion.numero_folio_solicitud}`,
                        fecha: new Date(asignacion.fecha).toDate(),
                        observaciones: asignacion.observaciones,
                        registro: asignacion.usuario ? asignacion.usuario.nombre : '',
                        buttons: $.extend({}, {})
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
                handler () {
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

<style scoped>
    .money
    {
        text-align: right;
    }
    .th_money
    {
        width: 150px;
        max-width: 150px;
        min-width: 100px;
    }
</style>
