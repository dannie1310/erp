<template>
    <span>
        <div class="row">
            <div class="col-12">
                <router-link :to="{name: 'cargar-pasivos'}" v-if="$root.can('cargar_layouts_pasivos',true)" :disabled="cargando" class="btn btn-app float-right">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-plus" v-else></i>
                    Carga Layout
                </router-link>
            </div>
            <div class="col-12">
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" />
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </span>

</template>

<script>
    export default {
        name: "layout-pasivos-index",
        components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Nombre Archivo', field: 'nombre_archivo', thComp: require('../../globals/th-Filter').default},
                    { title: 'Usuario Cargo', field: 'usuario_carga', thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha / Hora Carga', field: 'fecha_hora_carga',tdClass: 'td_c150', thComp: require('../../globals/th-Filter').default},
                    { title: 'Acciones', field: 'buttons', tdClass: 'td_c70',  thClass: 'th_c70',   tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {
                    include: [],
                    sort: 'fecha_hora_carga',  order: 'desc'
                },
                search: '',
                cargando: false,


            }
        },

        mounted() {
            this.$Progress.start();

        },

        methods: {
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('contabilidadGeneral/layout-pasivo/SET_LAYOUTS', data.data);
                        this.$store.commit('contabilidadGeneral/layout-pasivo/SET_META', data.meta);
                })
                .finally(() => {
                    this.cargando = false;
                })
            },
        },

        computed: {
            layouts(){
                return this.$store.getters['contabilidadGeneral/layout-pasivo/layouts'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/layout-pasivo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            layouts: {
                handler(layouts) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = layouts.map((layout, i) => ({
                        index: (i + 1) + self.query.offset,
                        nombre_archivo: layout.nombre_archivo,
                        usuario_carga: layout.usuario_carga,
                        fecha_hora_carga: layout.fecha_hora_carga,
                        buttons: $.extend({}, {
                            id: layout.id,
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
