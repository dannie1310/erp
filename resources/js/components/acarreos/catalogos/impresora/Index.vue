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
        name: "impresora-index",
        components: { Create, DescargaLayout },
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index',thClass: 'th_index'},
                    { title: 'MAC', field: 'mac', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Marca', field: 'marca', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Modelo', field: 'modelo', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'created_at', sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Registró', field: 'registro', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estado', field: 'estatus', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'id', order: 'asc'},
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
                return this.$store.dispatch('acarreos/impresora/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/impresora/SET_IMPRESORAS', data.data);
                        this.$store.commit('acarreos/impresora/SET_META', data.meta);
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
            impresoras(){
                return this.$store.getters['acarreos/impresora/impresoras'];
            },
            meta(){
                return this.$store.getters['acarreos/impresora/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            impresoras: {
                handler(impresoras) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = impresoras.map((impresora, i) => ({
                        index: impresora.id,
                        mac: impresora.mac,
                        marca: impresora.marca,
                        modelo: impresora.modelo,
                        created_at: impresora.fecha_registro_format,
                        registro : impresora.usuario_registro,
                        estatus: this.getEstado(impresora.estado_format, impresora.estado_color),
                        buttons: $.extend({}, {
                            id: impresora.id,
                            activar: (impresora.estado === 0 && self.$root.can('activar_desactivar_impresora')) ? true : false,
                            desactivar: (impresora.estado === 1 && self.$root.can('activar_desactivar_impresora')) ? true : false,
                            edit: self.$root.can('editar_impresora') ? true : false,
                            show: self.$root.can('consultar_impresora') ? true : false,
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
