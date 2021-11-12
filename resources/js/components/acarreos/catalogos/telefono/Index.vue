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
    import Create from "./Create.vue";
    import DescargaLayout from "./DescargaLayout";
    export default {
        name: "telefono-index",
        components: {Create, DescargaLayout},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'IMEI Teléfono', field: 'imei',sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Id. Dispositivo', field: 'device_id', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Linea Telefónica', field: 'linea', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Marca', field: 'marca', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Modelo', field: 'modelo', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Asigando a Checador', field: 'checador', sortable: true},
                    { title: 'Fecha Registro', field: 'created_at', thClass: 'fecha_hora', sortable: true},
                    { title: 'Registró', field: 'registro', sortable: true},
                    { title: 'Estado', field: 'estatus', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'id', order: 'asc'},
                estado: "",
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
                return this.$store.dispatch('acarreos/telefono/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/telefono/SET_TELEFONOS', data.data);
                        this.$store.commit('acarreos/telefono/SET_META', data.meta);
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
            telefonos(){
                return this.$store.getters['acarreos/telefono/telefonos'];
            },
            meta(){
                return this.$store.getters['acarreos/telefono/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            telefonos: {
                handler(telefonos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = telefonos.map((telefono, i) => ({
                        index: (i + 1) + self.query.offset,
                        imei: telefono.imei,
                        device_id: telefono.id_dispositivo,
                        linea: telefono.linea,
                        marca: telefono.marca,
                        modelo: telefono.modelo,
                        checador : telefono.checador,
                        created_at: telefono.fecha_registro_format,
                        registro: telefono.usuario_registro,
                        estatus: this.getEstado(telefono.estado_format, telefono.estado_color),
                        buttons: $.extend({}, {
                            id: telefono.id,
                            show : self.$root.can('consultar_telefono') ? true : false,
                            activar: (parseInt(telefono.estado) == 0 && self.$root.can('activar_desactivar_telefono') ) ? true : false,
                            desactivar: (telefono.estado === 1 && self.$root.can('activar_desactivar_telefono')) ? true : false,
                            edit: self.$root.can('editar_telefono') ? true : false,
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

<style>

</style>