<template>
    <div class="row">
        <div class="col-12"  v-if="$root.can('registrar_autocorreccion_cfd_efo', true)" :disabled="cargando">
            <button @click="create" class="btn btn-app btn-info float-right">
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
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
    import Create from './Create'
    export default {
        name: "no-deducidos-cfd-efos-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Proveedor', field: 'proveedor', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Fecha', field: 'fecha', sortable: true},
                    { title: 'Estatus', field: 'estado', sortable: true, tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: ['proveedor'], sort: 'id', order: 'desc'},
                estado: "",
                cargando: false,
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
            create() {
                this.$router.push({name: 'no-deducidos-cfd-efos-create'});
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/autocorreccion/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('fiscal/autocorreccion/SET_AUTOCORRECCIONES', data.data);
                        this.$store.commit('fiscal/autocorreccion/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstadoCFD(estado, descripcion) {
                let val = parseInt(estado);
                switch (val) {
                    case 5:
                        return {
                            color: '#f39c12',
                            descripcion: descripcion
                        }
                    case 6:
                        return {
                            color: '#00a65a',
                            descripcion: descripcion
                        }
                }
            }
        },
        computed: {
            autocorrecciones(){
                return this.$store.getters['fiscal/autocorreccion/autocorrecciones'];
            },
            meta(){
                return this.$store.getters['fiscal/autocorreccion/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            autocorrecciones: {
                handler(autocorrecciones) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = autocorrecciones.map((autocorreccion, i) => ({
                        index: (i + 1) + self.query.offset,
                        proveedor: autocorreccion.proveedor.razon_social,
                        fecha: autocorreccion.fecha,
                        estado: this.getEstadoCFD(autocorreccion.estatus.id, autocorreccion.estatus.descripcion),
                        buttons: $.extend({}, {
                            id: autocorreccion.id,
                            aplicar: (self.$root.can('aplicar_autocorreccion_cfd_efo',true) && autocorreccion.estatus.id == 5) ? true : false,
                            show : self.$root.can('consultar_autocorreccion_cfd_efo', true) ? true : false
                        })
                    }));
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

<style scoped>

</style>
