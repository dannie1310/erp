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
        name: "empresa-acarreos-index",
        components: {Create, DescargaLayout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Razón Social', field: 'razonSocial',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'RFC', field: 'rfc', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha y hora registro', field: 'created_at', sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Registró', field: 'usuario_registro', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estatus', field: 'estatus', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdEmpresa', order: 'asc'},
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
                return this.$store.dispatch('acarreos/empresa/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/empresa/SET_EMPRESAS', data.data);
                        this.$store.commit('acarreos/empresa/SET_META', data.meta);
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
            empresas(){
                return this.$store.getters['acarreos/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['acarreos/empresa/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            empresas: {
                handler(empresas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = empresas.map((empresa, i) => ({
                        index: (i + 1) + self.query.offset,
                        razonSocial: empresa.razon_social,
                        rfc: empresa.rfc,
                        created_at: empresa.fecha_registro,
                        usuario_registro :  empresa.nombre_registro,
                        estatus: this.getEstado(empresa.estado_format, empresa.estado_color),
                        buttons: $.extend({}, {
                            id: empresa.id,
                            edit: self.$root.can('editar_empresa') ? true : false,
                            activar: (empresa.estado === 0 && self.$root.can('activar_desactivar_empresa')) ? true : false,
                            desactivar: (empresa.estado === 1 && self.$root.can('activar_desactivar_empresa')) ? true : false,
                            show : self.$root.can('consultar_empresa') ? true : false,
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
