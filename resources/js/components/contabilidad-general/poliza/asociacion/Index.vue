<template>
    <span>
        <div class="card" v-if="buscando">
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
                                <span style="font-weight: bold;">{{this.currentEmpresa.Nombre}}</span>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group btn-group-toggle pull-right" data-toggle="buttons">
                                    <label class="btn btn-primary active">
                                        <input type="checkbox" autocomplete="off"
                                               v-model="sin_cfdi" :disabled="cargando"> Sin CFDI
                                    </label>
                                    <label class="btn btn-primary active">
                                        <input type="checkbox" autocomplete="off"
                                               v-model="con_cfdi" :disabled="cargando"> Con CFDI
                                    </label>
                                </div>
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
        props: [],
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
                    { title: 'Ejercicio', field: 'ejercicio', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../../globals/th-Filter').default,  sortable: true },
                    { title: 'Periodo', field: 'periodo', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha', field: 'fecha', tdClass: 'td_fecha', thClass: 'th_fecha', sortable: true },
                    { title: 'Tipo', field: 'tipopol', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../../globals/th-Filter').default, sortable: true },
                    { title: 'Folio', field: 'folio', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Monto', field: 'cargos', tdClass: 'td_money td_c90', thClass: 'th_c90', thComp: require('../../../globals/th-Filter').default, sortable: true},
                    { title: 'Concepto', field: 'concepto',thComp: require('../../../globals/th-Filter').default, sortable: false},
                    { title: '# CFDI', field: 'cantidad_cfdi',tdClass: 'right',sortable: false},
                    { title: 'Usuario', field: 'usuario_codigo',tdClass: 'td_c90',sortable: false},
                    { title: 'Acciones', field: 'buttons', tdClass: 'td_c120',  thClass: 'th_c120',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {sort:'fecha',order:'desc'},
                search: '',
                file:'',
                nombre: '',
                sin_cfdi : true,
                con_cfdi : true,

            }
        },

        computed: {
            polizas(){
                return this.$store.getters['contabilidadGeneral/poliza/polizas'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/poliza/meta'];
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
                        numero: poliza.folio,
                        ejercicio: poliza.ejercicio,
                        periodo: poliza.periodo,
                        fecha: poliza.fecha,
                        tipopol: poliza.tipo,
                        folio: poliza.folio,
                        cargos: poliza.cargos,
                        concepto: poliza.concepto,
                        cantidad_cfdi: poliza.cantidad_cfdi,
                        usuario_codigo: poliza.usuario_codigo,
                        buttons: $.extend({}, {
                            id: poliza.id,
                            id_empresa: this.currentEmpresa.Id,
                            editar:self.$root.can('editar_poliza',true) ? true : undefined,
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
            sin_cfdi(sin_cfdi) {
                if(!sin_cfdi && !this.con_cfdi)
                {
                    this.query.scope = '';
                }else if(!sin_cfdi && this.con_cfdi)
                {
                    this.query.scope = 'conCfdi';
                }else if(sin_cfdi && !this.con_cfdi)
                {
                    this.query.scope = 'sinCfdi';
                }else{
                    this.query.scope = '';
                }
                this.query.offset = 0;
                this.getPolizas();
            },

            con_cfdi(con_cfdi) {
                if(!this.sin_cfdi && !con_cfdi)
                {
                    this.query.scope = '';
                }else if(!this.sin_cfdi && con_cfdi)
                {
                    this.query.scope = 'conCfdi';
                }else if(this.sin_cfdi && !con_cfdi)
                {
                    this.query.scope = 'sinCfdi';
                }else{
                    this.query.scope = '';
                }
                this.query.offset = 0;
                this.getPolizas();
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
                //this.buscando = true;
                this.cargando = true;
                this.$Progress.start();
                if(this.polizas)
                {
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZAS', []);
                }
                return this.$store.dispatch('contabilidadGeneral/poliza/paginate',
                    {
                        params: this.query
                    })
                    .then(data => {
                        this.empresa = data.data[0].empresa;
                        this.encontradas = true;
                        this.$store.commit('contabilidadGeneral/poliza/SET_POLIZAS', data.data);
                        this.$store.commit('contabilidadGeneral/poliza/SET_META', data.meta);
                    }).finally(() => {
                        this.cargando = false;
                        this.$Progress.finish();
                    });

            },
            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
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
