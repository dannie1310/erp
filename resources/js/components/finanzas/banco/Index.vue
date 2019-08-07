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
        name: "banco-index",
        components: {Create},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Razón Social', field: 'razon_social', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {
                    scope:'BancosCtg', sort: 'id_empresa',  order: 'desc'
                },
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
            paginate(){
                this.cargando=true;
                return this.$store.dispatch('cadeco/empresa/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('cadeco/empresa/SET_EMPRESAS', data.data);
                        this.$store.commit('cadeco/empresa/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            empresas(){
                return this.$store.getters['cadeco/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['cadeco/empresa/meta']
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
                    empresas.forEach(function (empresa, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            razon_social: empresa.razon_social,
                            buttons: $.extend({}, {
                                show: true,
                                edit: true,
                                id: empresa.id
                            })
                        })

                    });

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
        },
    }
</script>
