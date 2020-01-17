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
        name: "destajista-index",
        components: {Create},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'R.F.C.', field: 'rfc', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Razón Social', field: 'razon_social', sortable: true, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Condición de Pago (días)', field: 'dias_credito',  tdClass: 'td_money', thClass: 'th_money',  sortable: true},
                    { title: 'Estado EFOS', field: 'efo', tdComp: require('./partials/EfoEstatus').default,  thComp: require('../../../globals/th-Filter').default},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {sort: 'id_empresa', order: 'desc'},
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
                return this.$store.dispatch('cadeco/destajista/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('cadeco/destajista/SET_DESTAJISTAS', data.data);
                        this.$store.commit('cadeco/destajista/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            destajistas(){
                return this.$store.getters['cadeco/destajista/destajistas'];
            },
            meta(){
                return this.$store.getters['cadeco/destajista/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            destajistas: {
                handler(destajistas) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = destajistas.map((destajista, i) => ({
                        index: (i + 1) + self.query.offset,
                        rfc: destajista.rfc,
                        razon_social: destajista.razon_social,
                        dias_credito: destajista.dias_credito,
                        efo :  typeof destajista.efo !== 'undefined' ?  destajista.efo.estado : '',
                        buttons: $.extend({}, {
                            edit: self.$root.can('editar_destajista') ? true : undefined,
                            show: self.$root.can('consultar_destajista') ? true : undefined,
                            delete: self.$root.can('eliminar_destajista') ? true : undefined,
                            id: destajista.id
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
<style>
    .money
    {
        text-align: right;
    }
</style>
