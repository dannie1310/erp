<template>
    <div class="row">
        <div class="col-12">
            <button @click="create_solicitud" v-if="" class="btn btn-app btn-info pull-right">
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
  import Create from './Create';
    export default {
        name: "solicitud-compra-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true },
                    { title: 'Observaciones', field: 'observaciones', sortable: true },
                    { title: 'Registró', field: 'registro', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},


                ],
                data: [],
                total: 0,
                query: {sort: 'id_transaccion',  order: 'desc'},
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
                return this.$store.dispatch('compras/solicitud-compra/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/solicitud-compra/SET_SOLICITUDES', data.data);
                        this.$store.commit('compras/solicitud-compra/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create_solicitud() {
                this.$router.push({name: 'solicitud-compra-create'});
            },

        },
        computed: {
            solicitudes(){
                return this.$store.getters['compras/solicitud-compra/solicitudes'];
            },

            meta(){
                return this.$store.getters['compras/solicitud-compra/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            solicitudes: {
                handler(solicitudes) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = solicitudes.map((solicitud, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: `# ${solicitud.numero_folio}`,
                        fecha: new Date(solicitud.fecha).toDate(),
                        observaciones: solicitud.observaciones,
                        id_usuario: solicitud.usuario ? solicitud.usuario.nombre : '',
                        buttons: $.extend({}, {
                            id: solicitud.id,
                            show: true,
                            edit: true,
                            pdf: true,

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
