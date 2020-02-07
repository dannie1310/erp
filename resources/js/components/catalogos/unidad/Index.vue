<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
        </div>
        <div class="col-md-3 offset-4 centered">
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
    import Create from "./Create"
    export default {
        name: "unidad-index",
        components:{Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_icono', sortable: false },
                    { title: 'Unidad', field: 'unidad', thClass: 'td_unidad',  sortable: true },
                    { title: 'Descripcion', field: 'descripcion', thClass: 'td_unidad', thComp: require('../../globals/th-Filter').default, sortable: true }
                ],
                data: [],
                total: 0,
                query: {},
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
                return this.$store.dispatch('cadeco/unidad/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('cadeco/unidad/SET_UNIDADES', data.data);
                        this.$store.commit('cadeco/unidad/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            unidades(){
                return this.$store.getters['cadeco/unidad/unidades'];
            },

            meta(){
                 return this.$store.getters['cadeco/unidad/meta'];
            },

            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            unidades: {
                handler(unidades) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = unidades.map((unidad, i) => ({
                        index: (i + 1) + self.query.offset,
                        unidad: unidad.unidad,
                        descripcion: unidad.descripcion,
                        buttons: $.extend({}, {
                                id: unidad.unidad,
                                unidad: unidad,
                                borrar: true,
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

