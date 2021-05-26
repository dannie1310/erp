<template>
   <div class="row">
        <div class="col-12">
            <Create @created="paginate()" />
            <!-- <DescargaLayout /> -->
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
   //  import DescargaLayout from "./DescargaLayout";
    export default {
        name: "operador-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: 'ID', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Nombre', field: 'Nombre',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Dirección', field: 'Direccion',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'No. Licencia', field: 'NoLicencia',sortable: true, thClass:'th_c150', thComp: require('../../../globals/th-Filter').default},
                    { title: 'Vigencia Licencia', field: 'VigenciaLicencia',  thClass: 'th_c120',sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Registró', field: 'usuario_registro', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'created_at', tdClass: 'fecha_hora', thClass: 'fecha_hora', sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Estado', field: 'estatus', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons', thClass:'th_c100',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdOperador', order: 'asc'},
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
                return this.$store.dispatch('acarreos/operador/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/operador/SET_OPERADORES', data.data);
                        this.$store.commit('acarreos/operador/SET_META', data.meta);
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
            operadores(){
                return this.$store.getters['acarreos/operador/operadores'];
            },
            meta(){
                return this.$store.getters['acarreos/operador/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            operadores: {
                handler(operadores) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = operadores.map((operador, i) => ({
                        index:  operador.clave_format,
                        Nombre: operador.nombre,
                        Direccion: operador.direccion,
                        NoLicencia: operador.no_licencia,
                        VigenciaLicencia: operador.licencia_vigencia_format,
                        usuario_registro : operador.usuario_registro,
                        created_at: operador.fecha_registro_format,
                        estatus: this.getEstado(operador.estado_format, operador.estado_color),
                        buttons: $.extend({}, {
                            id: operador.id,
                           //  activar: (operador.estado === 0 && self.$root.can('activar_desactivar_operador'))||true ? true : false,
                           //  desactivar: (operador.estado === 1 && self.$root.can('activar_desactivar_operador'))||true ? true : false,
                            activar: (operador.estado === 0 ) ? true : false,
                            desactivar: (operador.estado === 1) ? true : false,
                            edit: self.$root.can('editar_operador')||true ? true : false,
                            show: self.$root.can('consultar_operador')||true ? true : false,
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
