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
        name: "autorizar-pago-factura-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Proyecto', field: 'proyecto',sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Número Folio', field: 'numero_folio', tdClass:'center', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Año', field: 'anio', tdClass:'center', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Semana', tdClass:'center', field: 'numeroSemana', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Número de Remesa', tdClass:'center', field: 'numeroRemesa', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha', tdClass:'center', field: 'fecha', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Proveedor', tdClass:'center', field: 'proveedor', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'RFC', tdClass:'center', field: 'rfc', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Referencia', tdClass:'center', field: 'refencia', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Moneda', tdClass:'center', field: 'moneda', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Monto', field: 'monto',  tdClass: 'money', thClass: 'th_money', sortable: true},
                   // { title: 'Acciones', field: 'buttons',  thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {include: 'documento.remesaSinScope', sort: '', order: ''},
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
                return this.$store.dispatch('remesas/documento-no-localizado/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTOS', data.data);
                        this.$store.commit('remesas/documento-no-localizado/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
        },
        computed: {
            documentos(){
                return this.$store.getters['remesas/documento-no-localizado/documentos'];
            },
            meta(){
                return this.$store.getters['remesas/documento-no-localizado/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            documentos: {
                handler(documentos) {
                    let self = this
                    self.$data.data = []
                    self.$data.data = documentos.map((documento, i) => ({
                        index: (i + 1) + self.query.offset,
                        proyecto: documento.documento.remesaSinScope.proyecto_descripcion,
                        numero_folio: documento.documento.numero_folio,
                        anio: documento.documento.remesaSinScope.año,
                        fecha: documento.documento.fecha,
                        numeroSemana: documento.documento.remesaSinScope.semana,
                        numeroRemesa: documento.documento.remesaSinScope.folio,
                        proveedor: documento.documento.proveedor,
                        rfc: documento.documento.rfc,
                        refencia: documento.documento.refencia,
                        moneda: documento.documento.moneda,
                        monto: documento.documento.monto,

                        /*buttons: $.extend({}, {
                            anio: folio.anio,
                            semana: folio.numero_semana,
                            id_proyecto : folio.id_proyecto,
                            edit: self.$root.can('editar_limite_remesa') ? true : true,
                        })*/
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
