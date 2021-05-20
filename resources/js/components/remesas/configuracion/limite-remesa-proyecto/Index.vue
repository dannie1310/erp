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
        name: "limite-remesa-proyecto-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Obra SAO', field: 'obra',sortable: false, },
                    { title: 'Proyecto Remesa', field: 'nombre',sortable: false, },
                    { title: 'Empresa', field: 'empresa',sortable: false, },
                    { title: 'Tipo', field: 'tipo',sortable: false, },
                    { title: 'Cantidad Límite Extraordinarias', field: 'cantidad_limite_extraordinarias',  tdClass: 'money', thClass: 'th_money', sortable: false},
                    { title: 'Acciones', field: 'buttons',  thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'activo', sort: 'Nombre', order: 'asc'},
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
                return this.$store.dispatch('remesas/proyecto/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('remesas/proyecto/SET_PROYECTOS', data.data);
                        this.$store.commit('remesas/proyecto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            proyectos(){
                return this.$store.getters['remesas/proyecto/proyectos'];
            },
            meta(){
                return this.$store.getters['remesas/proyecto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            proyectos: {
                handler(proyectos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = proyectos.map((proyecto, i) => ({
                        index: (i + 1) + self.query.offset,
                        nombre: proyecto.nombre,
                        empresa: (proyecto.empresa)? proyecto.empresa.descripcion :'',
                        obra: proyecto.obra,
                        tipo: (proyecto.tipo)? proyecto.tipo.descripcion :'',
                        cantidad_limite_extraordinarias : proyecto.cantidad_limite_extraordinarias,
                        buttons: $.extend({}, {
                            id_proyecto : proyecto.id,
                            edit: self.$root.can('editar_limite_remesa_proyecto') ? true : true,
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
