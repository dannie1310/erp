<template>
    <div class="row">
        <div class="col-12">
            <button @click="create_requisicion" v-if="$root.can('registrar_requisicion_compra')" class="btn btn-app btn-info pull-right">
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
        name: "IndexInvitacionCompra",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Folio', field: 'numero_folio', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha', field: 'fecha', thComp: require('../../globals/th-Date').default, sortable: true },
                    { title: 'Folio Solicitud', field: 'numero_folio_solicitud', sortable: true },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},


                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'id',  order: 'desc'},
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
                return this.$store.dispatch('compras/invitacion/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('compras/invitacion/SET_INVITACIONES', data.data);
                        this.$store.commit('compras/invitacion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            create_invitacion() {
                this.$router.push({name: 'invitacion-create'});
            },

        },
        computed: {
            invitaciones(){
                return this.$store.getters['compras/invitacion/invitaciones'];
            },

            meta(){
                return this.$store.getters['compras/invitacion/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            invitaciones: {
                handler(invitaciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = invitaciones.map((invitacion, i) => ({
                        index: (i + 1) + self.query.offset,
                        numero_folio: invitacion.numero_folio_format,
                        numero_folio_solicitud: invitacion.transaccion.numero_folio_format,
                        fecha: new Date(invitacion.fecha).toDate(),
                        id_usuario: invitacion.nombre_usuario_invito,
                        buttons: $.extend({}, {
                            id: invitacion.id,
                            show: true,
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
