<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <button @click="detectar" class="btn btn-warning float-right">
                        <i class="fa fa-not-equal"></i> Detectar Diferencias
                 </button>
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
                cargando: false,
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', thClass: 'th_index_corto', sortable: false },
                    { title: 'Folio', field: 'id', thClass: 'th_folio', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Tipo Diferencia', field: 'tipo', thClass: 'fecha_hora', sortable: true},
                    { title: 'Fecha / Hora Detección', field: 'fecha_hora_deteccion', thClass: 'fecha_hora', sortable: true},
                    { title: 'Base de Datos Revisada', field: 'base_datos', sortable: true},
                    { title: 'Base de Datos Referencia', field: 'base_datos_referencia', sortable: true},
                    { title: 'Ejercicio', field: 'ejercicio', sortable: true},
                    { title: 'Periodo', field: 'periodo', sortable: true},
                    { title: 'Tipo Poliza', field: 'tipo_poliza', sortable: true},
                    { title: 'Folio Póliza', field: 'folio_poliza', sortable: true},
                    { title: 'Detalle de Error', field: 'observaciones', sortable: true},

                ],
                data: [],
                total: 0,
                query: {scope:['activos'], include:['poliza'], sort: 'id', order: 'desc'},
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
                            base_datos:incidente.base_datos,
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
            }
        }
    }
</script>

<style scoped>

</style>