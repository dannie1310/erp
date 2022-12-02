<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <button @click="detectar" class="btn btn-warning float-right">
                        <i class="fa fa-not-equal"></i> Detectar Diferencias
                 </button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-row">
                            <div class="col">
                                <select class="form-control" v-model="id_empresa">
                                    <option value>-- Empresa --</option>
                                    <option v-for="item in empresas" v-bind:value="item.id">{{ item.razon_social }}</option>
                                </select>
                            </div>

                            <div class="col">
                                <select class="form-control" v-model="id_empresa_contabilidad">
                                    <option value>-- Empresa Contabilidad --</option>
                                    <option v-for="item in empresas_contabilidad" v-bind:value="item.id">{{ item.nombre }}</option>
                                </select>
                            </div>

                            <div class="col">
                                <select class="form-control" v-model="id_tipo_diferencia">
                                    <option value>-- Tipo de Diferencia --</option>
                                    <option v-for="item in tiposDiferencia" v-bind:value="item.id">{{ item.descripcion }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <datatable v-bind="$data" />
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Index",
        data() {
            return {
                tiposDiferencia: [
                    {"id" : 1, "descripcion":"Póliza No Encontrada"},
                    {"id" : 2, "descripcion":"Diferente Concepto en Póliza"},
                    {"id" : 3, "descripcion":"Diferente Suma Cargos / Abonos"},
                    {"id" : 4, "descripcion":"Diferente No. Movimientos"},
                    {"id" : 5, "descripcion":"Movimiento No Encontrado"},
                    {"id" : 6, "descripcion":"Diferente Código de Cuenta"},
                    {"id" : 7, "descripcion":"Diferente Nombre de Cuenta"},
                    {"id" : 8, "descripcion":"Diferente Referencia"},
                    {"id" : 9, "descripcion":"Diferente Concepto en Movimiento"},
                    {"id" : 10, "descripcion":"Diferente Tipo de Movimiento"},
                    {"id" : 11, "descripcion":"Diferente Importe en Movimiento"},
                    {"id" : 12, "descripcion":"Movimientos Desordenados"},
                ],
                cargando: false,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Folio', field: 'id', thClass: 'th_folio', sortable: true},
                    { title: 'Tipo Diferencia', field: 'tipo', thClass: 'fecha_hora', sortable: false},
                    { title: 'Fecha / Hora Detección', field: 'fecha_hora_deteccion', thClass: 'fecha_hora', sortable: true},
                    { title: 'Base de Datos Revisada', field: 'base_datos_revisada', sortable: true},
                    { title: 'Base de Datos Referencia', field: 'base_datos_referencia', sortable: true},
                    { title: 'Ejercicio', field: 'ejercicio', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Periodo', field: 'periodo', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Tipo Poliza', field: 'tipo_poliza', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Folio Póliza', field: 'folio_poliza', sortable: false, thComp: require('../../../globals/th-Filter').default},
                    { title: 'Detalle de Error', field: 'observaciones', sortable: true, thComp: require('../../../globals/th-Filter').default},

                ],
                data: [],
                total: 0,
                query: {scope:['activos'], include:['poliza'], sort: 'id', order: 'desc'},
                id_tipo_diferencia: '',
                id_empresa:'',
                id_empresa_contabilidad:'',
                empresas:{},
                empresas_contabilidad:{},
            }
        },
        mounted() {
            this.getEmpresasContabilidad();
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },
        methods: {
            detectar() {
                this.$router.push({name: 'detectar-diferencias-polizas'});
            },
            paginate() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/incidente-poliza/paginate', { params: this.query})
                    .then(data => {
                        this.$store.commit('contabilidadGeneral/incidente-poliza/SET_INCIDENTES', data.data);
                        this.$store.commit('contabilidadGeneral/incidente-poliza/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },
            getEmpresasContabilidad() {
                this.empresas_contabilidad = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                    params: {
                        sort: 'Nombre',
                        order: 'asc',
                        scope:'solicitudes',
                    }
                })
                    .then(data => {
                        this.empresas_contabilidad = data.data;

                    }).finally( ()=>{
                        this.getEmpresas();
                    });
            },

            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa-sat/index', {
                    params: {
                        sort: 'razon_social',
                        order: 'asc',
                        scope:'solicitudes',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;

                    }).finally( ()=>{

                        this.cargando = false;
                    });
            },

        },
        computed: {
            incidentes(){
                return this.$store.getters['contabilidadGeneral/incidente-poliza/incidentes'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/incidente-poliza/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        },
        watch: {
            incidentes: {
                handler(incidentes) {
                    let self = this
                    self.$data.data = []
                    incidentes.forEach(function (incidente, i) {
                        self.$data.data.push({
                            index: (i + 1) + self.query.offset,
                            id:incidente.id,
                            fecha_hora_deteccion:incidente.fecha_hora_deteccion_format,
                            base_datos_revisada:incidente.base_datos,
                            base_datos_referencia:incidente.base_datos_referencia,
                            ejercicio:incidente.poliza.ejercicio,
                            periodo:incidente.poliza.periodo,
                            tipo_poliza:incidente.poliza.tipo,
                            folio_poliza:incidente.poliza.folio,
                            tipo:incidente.tipo_incidente,
                            observaciones:incidente.observaciones
                        })
                    });
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
            },
            id_tipo_diferencia(id_tipo) {
                this.$data.query.id_tipo_diferencia = id_tipo;
                this.query.offset = 0;
                this.paginate()
            },
            id_empresa_contabilidad(id_empresa_contabilidad) {
                this.$data.query.id_empresa_contabilidad = id_empresa_contabilidad;
                this.query.offset = 0;
                this.paginate()
            },
            id_empresa(id_empresa) {
                this.$data.query.id_empresa = id_empresa;
                this.query.offset = 0;
                this.paginate()
            },
        }
    }
</script>

<style scoped>

</style>
