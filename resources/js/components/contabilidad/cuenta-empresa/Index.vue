<template>
    <div class="row">
        <div class="col-12">
            <create @created="created($event)"></create>
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
        name: "cuenta-empresa-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Empresa', field: 'razon_social', sortable: true },
                    { title: 'Cuentas Registradas', field: 'cuentas_count', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
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
            this.query.include = 'cuentasEmpresa'
            this.query.scope = 'conCuentas'
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            created(data) {
                this.$store.commit('cadeco/empresa/SET_CUENTA_EMPRESA', data)
            },

            paginate() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/empresa/paginate', { params: this.query })
                    .then(data => {
                        this.$store.commit('cadeco/empresa/SET_EMPRESAS', data.data);
                        this.$store.commit('cadeco/empresa/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            empresas(){
                return this.$store.getters['cadeco/empresa/empresas'];
            },
            meta(){
                return this.$store.getters['cadeco/empresa/meta'];
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
                        razon_social: empresa.razon_social,
                        cuentas_count: empresa.cuentasEmpresa.data.length,
                        buttons: $.extend({}, {
                            show: true,
                            edit: self.$root.can('editar_cuenta_empresa') ? true : undefined,
                            id: empresa.id
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
