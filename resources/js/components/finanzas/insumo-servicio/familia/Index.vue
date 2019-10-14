<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
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
    import Create from "./Create";
    export default {
        name: "familia-serv-index",
        components:{Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                 //   { title: 'Familia', field: 'tipo_material',sortable: true},
                    { title: 'Descripción', field: 'descripcion', sortable: true, thComp: require('../../../globals/th-Filter')},
                    // { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')}
                ],
                data: [],
                total: 0,
                query: { sort: 'id_material', order: 'desc'},
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

                return this.$store.dispatch('finanzas/familia-serv/porServicio', {
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('finanzas/familia-serv/SET_FAMILIAS',data.data);
                        this.$store.commit('finanzas/familia-serv/SET_META', {
                            "pagination": {
                                "total": data.total,
                                "count": data.to - data.from + 1,
                                "per_page": data.per_page,
                                "current_page": data.current_page,
                                "total_pages": data.last_page,
                            }
                        });
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            familias(){
                return this.$store.getters['finanzas/familia-serv/familias'];
            },
            meta(){
                return this.$store.getters['finanzas/familia-serv/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            familias: {
                handler(famls) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = famls.map((familia, i) => ({
                        index: (i + 1) + self.query.offset,
                        descripcion: familia.descripcion
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
    .money
    {
        text-align: right;
    }
</style>
