<template>
    <div class="row">
        <div class="col-12">
            <create @created="paginate()"></create>
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
        name: "cuenta-concepto-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Concepto', field: 'concepto', sortable: false },
                    { title: 'Clave del Concepto', field: 'clave', sortable: false },
                    { title: 'Ruta', field: 'ruta', sortable: false },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
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
                return this.$store.dispatch('contabilidad/cuenta-concepto/paginate',{
                    params: this.query
                })
                    .then(data => {
                        this.$store.commit('contabilidad/cuenta-concepto/SET_CUENTAS', data.data);
                        this.$store.commit('contabilidad/cuenta-concepto/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            }
        },

        computed: {
            cuentas(){
                return this.$store.getters['contabilidad/cuenta-concepto/cuentas'];
            },
            meta(){
                return this.$store.getters['contabilidad/cuenta-concepto/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },

        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = cuentas.map((cuenta, i) => ({
                        index: (i + 1) + self.query.offset,
                        cuenta: cuenta.cuenta,
                        concepto: cuenta.concepto.descripcion,
                        ruta: cuenta.concepto.path,
                        clave: cuenta.concepto.clave_concepto,
                        buttons: $.extend({}, {
                            edit: self.$root.can('editar_cuenta_concepto') ? true : undefined,
                            id: cuenta.id
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
        },
    }
</script>