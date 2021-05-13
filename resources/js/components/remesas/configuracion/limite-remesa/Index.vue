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
        name: "limite-remesa-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Proyecto', field: 'proyecto',sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Año', field: 'anio', tdClass:'center', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Número de Semana', tdClass:'center', field: 'numeroSemana', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Cantidad Límite Extraordinarias', field: 'CantidadExtraordinariasPermitidas',  tdClass: 'money', thClass: 'th_money', sortable: true},
                    { title: 'Monto Límite Extraordinarias', field: 'MontoLimiteExtraordinarias', tdClass: 'money', thClass: 'th_money', sortable: true},
                    { title: 'Acciones', field: 'buttons',  thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {scope:'ordenarPorAnioSemana', sort: '', order: ''},
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
                return this.$store.dispatch('remesas/remesa-folio/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('remesas/remesa-folio/SET_FOLIOS', data.data);
                        this.$store.commit('remesas/remesa-folio/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            folios(){
                return this.$store.getters['remesas/remesa-folio/folios'];
            },
            meta(){
                return this.$store.getters['remesas/remesa-folio/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            folios: {
                handler(folios) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = folios.map((folio, i) => ({
                        index: (i + 1) + self.query.offset,
                        proyecto: folio.proyecto,
                        anio: folio.anio,
                        numeroSemana: folio.numero_semana,
                        CantidadExtraordinariasPermitidas: folio.cantidad_limite,
                        MontoLimiteExtraordinarias: folio.monto_limite,
                        buttons: $.extend({}, {
                            anio: folio.anio,
                            semana: folio.numero_semana,
                            id_proyecto : folio.id_proyecto,
                            edit: self.$root.can('editar_limite_remesa') ? true : true,
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
