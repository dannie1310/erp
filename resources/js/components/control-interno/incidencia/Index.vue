<template>
    <div class="row">
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
    export default {
        name: "incidencia-index",
        data() {
            return{
                HeaderSettings: false,
                columns: [
                    { title: '#', field:'index',sortable: false},
                    { title: 'Obra', field: 'obra', sortable: true},
                    { title: 'Base de Datos', field: 'base_datos', sortable: false},
                    { title: 'Tipo de Incidencia', field: 'id_tipo_incidencia',sortable: true},
                    { title: 'Usuario', field: 'id_usuario', sortable: false}
                ],
                data: [],
                total: 0,
                query: {
                    include: ['tipo', 'usuario'], sort: 'id',  order: 'desc'
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
                return this.$store.dispatch('seguridad/control-interno/incidencia/paginate', {params: this.query})
                    .then(data=>{
                        this.$store.commit('seguridad/control-interno/incidencia/SET_INCIDENCIAS', data.data);
                        this.$store.commit('seguridad/control-interno/incidencia/SET_META',data.meta)
                    })
                    .finally(()=>{
                        this.cargando=false;
                    })

            }
        },
        computed: {
            incidencias(){
                return this.$store.getters['seguridad/control-interno/incidencia/incidencias'];
            },
            meta(){
                return this.$store.getters['seguridad/control-interno/incidencia/meta']
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            incidencias: {
                handler(incidencias) {
                    let self = this
                    self.$data.data = []
                    incidencias.forEach(function (incidencia, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            obra: incidencia.obra,
                            base_datos: incidencia.base_datos,
                            id_tipo_incidencia: incidencia.tipo.description,
                            id_usuario: incidencia.usuario.nombre                            
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
