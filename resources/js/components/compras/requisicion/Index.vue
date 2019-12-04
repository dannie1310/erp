<template>
    <div class="row">
        <div class="col-12">
            <button @click="create_requisicion" v-if="$root.can('registrar_requisicion_compra')" class="btn btn-app btn-info pull-right">
                <i class="fa fa-plus"></i> Registrar Requisición
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
    import Create from './Create';
    export default {
        name: "requisicion-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Folio Compuesto', field: 'folio'},
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true },
                    { title: 'Observaciones', field: 'observaciones', sortable: true },
                    { title: 'Registró', field: 'id_usuario', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},


                ],
                data: [],
                total: 0,
                query: {include: ['registro', 'complemento'], sort: 'id_transaccion',  order: 'desc'},
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
                return this.$store.dispatch('compras/requisicion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/requisicion/SET_REQUISICIONES', data.data);
                        this.$store.commit('compras/requisicion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create_requisicion() {
                this.$router.push({name: 'requisicion-create'});
            },

        },
        computed: {
            requisiciones(){
                return this.$store.getters['compras/requisicion/requisiciones'];
            },

            meta(){
                return this.$store.getters['compras/requisicion/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            requisiciones: {
                handler(requisiciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = requisiciones.map((requisicion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: `# ${requisicion.numero_folio}`,
                        folio: requisicion.complemento ? requisicion.complemento.folio : '',
                        fecha: new Date(requisicion.fecha).toDate(),
                        observaciones: requisicion.observaciones,
                        id_usuario: requisicion.registro ? requisicion.registro.nombre : '',
                        buttons: $.extend({}, {
                            id: requisicion.id,
                            show: true,
                            delete: self.$root.can('eliminar_requisicion_compra') ? true : false
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

</style>
