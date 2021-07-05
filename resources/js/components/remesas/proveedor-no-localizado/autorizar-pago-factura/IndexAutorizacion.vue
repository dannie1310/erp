<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th colspan="4">Datos de Remesa</th>
                                    <th colspan="6">Datos de Transacción</th>
                                    <th rowspan="2" class="c100"></th>
                                </tr>
                                <tr>
                                    <th class="c150">Proyecto </th>
                                    <th class="c40">Año </th>
                                    <th>Semana </th>
                                    <th>Número </th>


                                    <th class="c80">Fecha </th>
                                    <th>Proveedor </th>
                                    <th class="rfc">RFC Proveedor </th>
                                    <th>Referencia </th>
                                    <th>Monto </th>
                                    <th>Moneda </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(documento, i) in data">
                                    <td>{{i+1}}</td>
                                    <td>
                                        {{documento.proyecto}}
                                    </td>
                                    <td>
                                        {{documento.anio}}
                                    </td>
                                    <td>
                                        {{documento.numeroSemana}}
                                    </td>
                                    <td>
                                        {{documento.numeroRemesa}}
                                    </td>
                                    <td>
                                        {{documento.fecha}}
                                    </td>
                                    <td>
                                        {{documento.proveedor}}
                                    </td>
                                    <td>
                                        {{documento.rfc}}
                                    </td>
                                    <td>
                                        {{documento.referencia}}
                                    </td>
                                    <td style="text-align: right">
                                        {{documento.monto}}
                                    </td>
                                    <td>
                                        {{documento.moneda}}
                                    </td>
                                    <td >
                                        <ActionButtons v-bind:value="documento.buttons"></ActionButtons>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
    import ActionButtons from "./partials/ActionButtons";
    export default {
        name: "autorizar-pago-factura-index-autorizacion",
        components: {ActionButtons},
        data() {
            return {
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index', tdClass: 'td_index', sortable: false },
                    { title: 'Proyecto', field: 'proyecto',sortable: false},
                    { title: 'Número Folio', field: 'numero_folio', tdClass:'center', sortable: false},
                    { title: 'Año', field: 'anio', tdClass:'center', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Semana', tdClass:'center', field: 'numeroSemana', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Número de Remesa', tdClass:'center', field: 'numeroRemesa', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Fecha', tdClass:'center', field: 'fecha', sortable: false},
                    { title: 'Proveedor', tdClass:'center', field: 'proveedor', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'RFC', tdClass:'center', field: 'rfc', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Referencia', tdClass:'center', field: 'referencia', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Moneda', tdClass:'center', field: 'moneda', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Monto', field: 'monto',  tdClass: 'money', thClass: 'th_money', sortable: true},
                    { title: 'Estado', field: 'estado', sortable: true, thClass:'th_c120', tdComp: require('./partials/EstatusLabel').default},
                    { title: 'Acciones', field: 'buttons',  thClass:'th_c100', tdClass:'center', tdComp: require('./partials/ActionButtons').default}
                ],
                data: [],
                total: 0,
                query: {include: 'documento.remesaSinScope', sort: '', order: '', scope:'autorizacionPendiente'},
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
                return this.$store.dispatch('remesas/documento-no-localizado/index', { params: this.query})
                    .then(data => {
                        this.$store.commit('remesas/documento-no-localizado/SET_DOCUMENTOS', data.data);
                        //this.$store.commit('remesas/documento-no-localizado/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEstado(estado, color) {
                return {
                    color: color,
                    descripcion: estado
                }
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
                        referencia: documento.documento.referencia,
                        moneda: documento.documento.moneda_nombre,
                        monto: documento.documento.monto_total_format,
                        estado: this.getEstado(documento.estado_format, documento.estado_color),
                        buttons: $.extend({}, {
                            id : documento.id,
                            autorizar: self.$root.can('autorizar_rechazar_transaccion_proveedor_no_localizado', true) && documento.estado == 0 ? true : false,
                            rechazar: self.$root.can('autorizar_rechazar_transaccion_proveedor_no_localizado', true) && documento.estado == 0 ? true : false,
                        })
                    }));
                },
                deep: true
            },

            meta: {
                handler(meta) {
                    /*let total = meta.pagination.total
                    this.$data.total = total*/
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
