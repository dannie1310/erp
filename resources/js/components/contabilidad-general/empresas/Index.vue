<template>
    <div class="row">
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
        name: "lista-empresas-index",
        components: {},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Nombre', field: 'nombre', sortable: true },
                    { title: 'Alias', field: 'alias', sortable: true },
                    { title: 'Visible', field: 'visible', sortable: true },
                    { title: 'Editable', field: 'editable', sortable: true },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
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
                    return this.$store.dispatch('seguridad/lista-empresas/paginate', { params: this.query })
                        .then(data => {
                            this.$store.commit('seguridad/lista-empresas/SET_EMPRESAS', data.data);
                            this.$store.commit('seguridad/lista-empresas/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            empresas(){
                return this.$store.getters['seguridad/lista-empresas/empresas'];
            },
            meta(){
                return this.$store.getters['seguridad/lista-empresas/meta'];
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
                    self.$data.data = empresas.map((empresa, i) => ({
                        index: (i + 1) + self.query.offset,
                        nombre: empresa.nombre,
                        alias: empresa.alias,
                        visible: empresa.visible == 1?'SI':'NO',
                        editable: empresa.editable == 1?'SI':'NO',
                        buttons: $.extend({}, {
                            edit: self.$root.can('configurar_visibilidad_empresa_ctpq', true) || self.$root.can('configurar_editabilidad_empresa_ctpq', true) ? true : false,
                            empresa: empresa,
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

<style>

</style>