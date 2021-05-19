<template>
    <div class="row">
        <div class="col-12">
            <Create @created="paginate()" />
            <DescargaLayout />
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
    import DescargaLayout from "./DescargaLayout";
    export default {
        name: "marca-index",
        components: {Create, DescargaLayout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'ID Marca', field: 'idMarca', thClass: 'th_100', tdClass: 'td_100', sortable: true, thComp: require('../../../globals/th-Filter').default },
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'created_at', sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Registró', field: 'usuario_registro', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estado', field: 'estatus', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdMarca', order: 'asc'},
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
                return this.$store.dispatch('acarreos/marca/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/marca/SET_MARCAS', data.data);
                        this.$store.commit('acarreos/marca/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
                }
            },
        },
        computed: {
            marcas(){
                return this.$store.getters['acarreos/marca/marcas'];
            },
            meta(){
                return this.$store.getters['acarreos/marca/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            marcas: {
                handler(marcas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = marcas.map((marca, i) => ({
                        index: (i + 1) + self.query.offset,
                        IdMarca: marca.clave_format,
                        descripcion: marca.descripcion,
                        created_at: marca.fecha_registro_format,
                        usuario_registro : marca.usuario_registro,
                        estatus: this.getEstado(marca.estado_format, marca.estado_color),
                        buttons: $.extend({}, {
                            id: marca.id,
                            activar: (marca.estado === 0 && self.$root.can('activar_desactivar_marca')) ? true : false,
                            desactivar: (marca.estado === 1 && self.$root.can('activar_desactivar_marca')) ? true : false,
                            edit: self.$root.can('editar_marca') ? true : false,
                            show: self.$root.can('consultar_marca') ? true : false,
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