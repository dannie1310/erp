<template>
    <div class="row">
        <div class="col-12">
          <cuenta-banco-create @created="paginate(query)"></cuenta-banco-create>
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
    import CuentaBancoCreate from "./Create";
    export default {
        name: "cuenta-banco-index",
        components: {CuentaBancoCreate},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Banco', field: 'razon_social', sortable: true },
                    { title: 'Cuentas Registradas', field: 'cuentas_count', sortable: false },
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons')},
                ],
                data: [],
                total: 0,
                query: {}
            }
        },
        mounted() {
            this.paginate()
            this.$store.dispatch('contabilidad/tipo-cuenta-contable/index')
        },
        methods: {
            paginate(payload = {}) {
                return this.$store.dispatch('cadeco/banco/paginate', {
                    ...payload,
                    include: 'cuentaBancaria'
                })
            }
        },
        computed: {
            bancos(){
                return this.$store.getters['cadeco/banco/bancos'];
            },
            meta(){
                return this.$store.getters['cadeco/banco/meta'];
            },
        },
        watch: {
            bancos: {
                handler(bancos) {
                    let self = this
                    self.$data.data = []
                    bancos.forEach(function (banco, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            razon_social: banco.razon_social,
                            cuentas_count: banco.cuentaBancaria.data.length,
                            buttons: $.extend({}, {
                                show: true,
                                edit: self.$root.can('editar_cuenta_banco') ? true : undefined,
                                id: banco.empresa.id
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
            }
        },
    }
</script>