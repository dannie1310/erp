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
    // import DescargaLayout from "./DescargaLayout";
    export default {
        name: "material-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha Registro', field: 'created_at',thClass: 'fecha_hora', sortable: true, thComp: require('../../../globals/th-Date').default},
                    { title: 'Registró', field: 'usuario_registro', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Estado', field: 'estatus', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',thClass: 'th_c150',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'', sort: 'IdMaterial', order: 'asc'},
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
                return this.$store.dispatch('acarreos/material/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('acarreos/material/SET_MATERIALES', data.data);
                        this.$store.commit('acarreos/material/SET_META', data.meta);
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
            materiales(){
                return this.$store.getters['acarreos/material/materiales'];
            },
            meta(){
                return this.$store.getters['acarreos/material/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            materiales: {
                handler(materiales) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = materiales.map((material, i) => ({
                        index: (i + 1) + self.query.offset,
                        descripcion: material.descripcion,
                        created_at: material.fecha_registro_format,
                        usuario_registro : material.usuario_registro,
                        estatus: this.getEstado(material.estado_format, material.estado_color),
                        buttons: $.extend({}, {
                            id: material.id,
                            activar: (material.estado === 0) ? true : false,
                            desactivar: (material.estado === 1) ? true : false,
                            // activar: (material.estado === 0 && self.$root.can('activar_desactivar_material'))|| true ? true : false,
                            // desactivar: (material.estado === 1 && self.$root.can('activar_desactivar_material')) || true ? true : false,
                            // edit: self.$root.can('editar_origen') ? true : false,
                            show: self.$root.can('consultar_material') || true ? true : false,
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