<template>
    <div class="row">
        <!--<div class="col-12">-->
        <!--<create @created="paginate()"></create>-->
        <!--</div>-->
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
        name: "cliente-index",
        // components:{Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'RFC', field: 'rfc', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Razon Social', field: 'razon_social', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Tipo Cliente', field: 'tipo_cliente', sortable: true},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {sort: 'razon_social', order: 'desc'},
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
                return this.$store.dispatch('cadeco/cliente/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('cadeco/cliente/SET_CLIENTES', data.data);
                        this.$store.commit('cadeco/cliente/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            clientes(){
                return this.$store.getters['cadeco/cliente/clientes'];
            },
            meta(){
                return this.$store.getters['cadeco/cliente/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            clientes: {
                handler(clientes) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = clientes.map((cliente, i) => ({
                        index: (i + 1) + self.query.offset,
                        rfc: cliente.rfc,
                        razon_social: cliente.razon_social,
                        tipo_cliente: cliente.tipo
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
