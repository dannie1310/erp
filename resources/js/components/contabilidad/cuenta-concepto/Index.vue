<template>
    <div class="row">
        <div class="col-12">
            <cuenta-concepto-create></cuenta-concepto-create>
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
    import CuentaConceptoCreate from "./Create";
    export default {
        name: "cuenta-concepto-index",
        components: {CuentaConceptoCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Cuenta', field: 'cuenta', sortable: true },
                    { title: 'Concepto', field: 'concepto', sortable: false },
                    { title: 'Ruta', field: 'ruta', sortable: false },
                    { title: 'Editar', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {},
                search: ''
            }
        },

        mounted() {
            this.query.include = 'concepto'
            this.paginate()
        },

        methods: {
            paginate() {
                let self = this
                self.$store.commit('contabilidad/cuenta-concepto/SET_CUENTAS', []);
                return self.$store.dispatch('contabilidad/cuenta-concepto/paginate', self.query)
                    .then(data => {
                        self.$store.commit('contabilidad/cuenta-concepto/SET_CUENTAS', data.data);
                        self.$store.commit('contabilidad/cuenta-concepto/SET_META', data.meta);
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
        },
        watch: {
            cuentas: {
                handler(cuentas) {
                    let self = this
                    self.$data.data = []
                    cuentas.forEach(function (cuenta, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            cuenta: cuenta.cuenta,
                            concepto: cuenta.concepto.descripcion,
                            ruta: cuenta.concepto.path,
                            buttons: $.extend({}, {
                                edit: self.$root.can('editar_cuenta_concepto') ? true : undefined,
                                id: cuenta.id
                            })
                        })
                    });
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
                    this.paginate();
                }, 500);
            },
        },
    }
</script>