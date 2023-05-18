<template>
    <div class="row">
        <div class="col-12">
            <router-link  :to="{ name: 'concurso-create'}"  v-if="$root.can('registrar_concurso', true)" type="button" class="btn btn-app pull-right" title="Registrar">
                <i class="fa fa-plus"></i> Registrar
            </router-link>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Buscar" v-model="search">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 15px'" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <router-view ></router-view>
    </div>
</template>

<script>
    import Registrar from './Create';
    export default {
        name: "concurso-index",
        components : {Registrar},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass:"th_index_corto", sortable: false },
                    { title: 'Nombre', field: 'nombre',  sortable: true, thComp: require('../../globals/th-Filter').default},
                    { title: 'Fecha de Apertura', field: 'fecha', tdClass: 'th_c90', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Fecha de Fallo', field: 'fecha_fallo', tdClass: 'th_c90', sortable: true, thComp: require('../../globals/th-Date').default},
                    { title: 'Estatus', field: 'estatus', thClass: 'th_c70', tdComp: require('./partials/EstatusLabel').default, sortable: true},
                    { title: 'Acciones', field: 'buttons', thClass: 'th_c70', tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {include: [], sort: 'id', order: 'desc'},
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
                return this.$store.dispatch('concursos/concurso/paginate', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('concursos/concurso/SET_CONCURSOS', data.data);
                        this.$store.commit('concursos/concurso/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;

                    })
            },
            getEstado(descripcion, color) {
                return {
                    color: color,
                    descripcion: descripcion
                }

            },
        },
        computed: {
            concursos(){
                return this.$store.getters['concursos/concurso/concursos'];
            },
            meta(){
                return this.$store.getters['concursos/concurso/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            concursos: {
                handler(concursos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = concursos.map((concurso, i) => ({
                        index: (i + 1) + self.query.offset,
                        nombre: concurso.nombre,
                        fecha: concurso.fecha_format,
                        fecha_fallo: concurso.fecha_fallo_format,

                        id: concurso.id,
                        estatus: this.getEstado(concurso.estatus_descripcion, concurso.estatus_color),
                        buttons: $.extend({}, {
                            id: concurso.id,
                            fallo: (self.$root.can('editar_concurso', true) && concurso.estatus == 2) ? true : false,
                            edit: (self.$root.can('editar_concurso', true) && concurso.estatus == 1) ? true : false,
                            close: (self.$root.can('cerrar_concurso', true) && concurso.estatus == 1) ? true : false,
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
                handler (query) {
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

body, .btn {
    font-size: 15px !important;
}

</style>



