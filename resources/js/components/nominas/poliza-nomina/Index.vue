<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                           <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span v-else>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <span style="font-weight: bold;">{{this.currentEmpresa.NombreEmpresa}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 12px'" />
                        </div>
                    </div>
                </div>
            </div>
        </span>
    </span>

</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "index-poliza",
    components: {ModelListSelect},
    props: [ 'id_empresa' ],
    data() {
        return {
            empresa : '',
            procesando:false,
            cargando: false,
            conectando:false,
            conectado:false,
            buscando:false,
            encontradas:false,
            HeaderSettings: false,
            columns: [
                { title: '#', field: 'index', sortable: false },
                { title: 'Ejercicio', field: 'ejercicio', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default,  sortable: true },
                { title: 'Fecha', field: 'fecha', tdClass: 'td_fecha', thClass: 'th_fecha', sortable: true },
                { title: 'Folio', field: 'numeropoliza', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default, sortable: true},
                { title: 'Concepto', field: 'concepto',thComp: require('../../globals/th-Filter').default, sortable: false},
                { title: 'Estado del XML', field: 'estado_nom', sortable: true, thClass:'th_c100', tdComp: require('./partials/EstatusLabel').default},
                { title: 'Acciones', field: 'buttons', tdClass: 'td_c120',  thClass: 'th_c120',  tdComp: require('./partials/ActionButtons').default},
            ],
            data: [],
            total: 0,
            query: {sort:'fechapoliza',order:'desc', scope: ['conGuid','limiteTiempo', 'estado:P']},
            search: '',
        }
    },

    computed: {
        polizas(){
            return this.$store.getters['nominas/poliza-contpaq/polizas'];
        },
        meta(){
            return this.$store.getters['nominas/poliza-contpaq/meta'];
        },
        tbodyStyle() {
            return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
        },
        currentEmpresa(){
            return this.$store.getters['auth/currentEmpresa']
        },
    },
    watch: {
        polizas: {
            handler(polizas) {
                let self = this
                self.$data.data = []
                self.$data.data = polizas.map((poliza, i) => ({
                    index: (i + 1) + self.query.offset,
                    ejercicio: poliza.ejercicio,
                    fecha: poliza.fecha_format,
                    numeropoliza: poliza.numeropoliza,
                    concepto: poliza.concepto,
                    estado_nom: this.getEstado(poliza.estado_log_format, poliza.estado_log_color),
                    buttons: $.extend({}, {
                        id : poliza.id,
                        id_empresa: this.currentEmpresa.IDEmpresa,
                        bd_empresa: this.currentEmpresa.RutaEmpresa,
                        xml_ifs : self.$root.can('descargar_xml_poliza_ifs_nomina_ctpq', true) ? true : false,
                        correo_xml_ifs : self.$root.can('enviar_correo_xml_poliza_ifs_nomina_ctpq', true) ? true : false,
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
            handler (query) {
                this.getPolizas()
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
                this.getPolizas();
            }, 500);
        },
        cargando(val) {
            $('tbody').css({
                '-webkit-filter': val ? 'blur(2px)' : '',
                'pointer-events': val ? 'none' : ''
            });
        }
    },
    methods: {
        getPolizas(){
            this.$Progress.start();
            this.query.id_empresa = this.currentEmpresa.Id;
            this.query.bd_empresa = this.currentEmpresa.RutaEmpresa;
            this.buscando = true;
            this.cargando = true;
            if(this.polizas)
            {
                this.$store.commit('nominas/poliza-contpaq/SET_POLIZAS', []);
            }
            return this.$store.dispatch('nominas/poliza-contpaq/paginate',
                {
                    params: this.query
                })
                .then(data => {
                    this.empresa = data.data[0].empresa;
                    this.encontradas = true;
                    this.$store.commit('nominas/poliza-contpaq/SET_POLIZAS', data.data);
                    this.$store.commit('nominas/poliza-contpaq/SET_META', data.meta);
                }).finally(() => {
                    this.cargando = false;
                    this.$Progress.finish();
                });

        },
        getEmpresas() {
            this.empresas = [];
            this.cargando = true;
            return this.$store.dispatch('nominas/empresa-contpaq/index', {
                params: {
                    sort: 'Nombre',
                    order: 'asc',
                    scope:'editable',
                }
            })
                .then(data => {
                    this.empresas = data.data;
                    this.cargando = false;
                })
        },
        getEstado(estado, color) {
            return {
                color: color,
                descripcion: estado
            }
        },
    }
}
</script>

<style scoped>

label:not(.form-check-label):not(.custom-file-label) {
    font-weight: 500;
}

.btn-primary:not(:disabled):not(.disabled):active, .btn-primary:not(:disabled):not(.disabled).active, .show > .btn-primary.dropdown-toggle {
    color: #ffffff;
    background-color: #007bff;
    border-color: #005cbf;
}

.btn-primary {
    color: #007bff;
    background-color: #ffffff;
    border-color: #dee2e6;
    box-shadow: none;
}

</style>
