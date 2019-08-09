<template>
    <div class="row">
        <div class="col-12">
        <create ></create>
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

    import Create from './Create';
    export default {
        name: "sucursal-index",
        components: {Create},
        data(){
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Descripción', field: 'descripcion', sortable: false},
                    { title: 'Dirección', field:'direccion', sortable: false},

                ],
                data: [],
                total: 0,
                query: {
                    // scope:'Bancos', sort: 'id_empresa',  order: 'desc'
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
                return this.$store.dispatch('cadeco/sucursal/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('cadeco/sucursal/SET_SUCURSALES', data.data);
                        this.$store.commit('cadeco/sucursal/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
          sucursales(){
                return this.$store.getters['cadeco/sucursal/sucursales'];
            },
            meta(){
                return this.$store.getters['cadeco/sucursal/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            sucursales: {
                handler(sucursales) {
                    let self = this
                    self.$data.data = []
                    sucursales.forEach(function (sucursal, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                           descripcion: sucursal.descripcion,
                            direccion: sucursal.direccion,
                            buttons: $.extend({}, {
                                 show: true,
                                id: sucursal.id
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
