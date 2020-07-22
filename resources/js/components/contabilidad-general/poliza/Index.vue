<template>
    <span>
     <!--   <div class="row">
            <div class="col-12">
                <show v-bind:tipo_modal="tipo_modal"></show>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <edit v-bind:tipo_modal="tipo_modal" v-bind:id_empresa="id_empresa"></edit>
            </div>
        </div>
-->
        <div class="row">
            <div class="col-md-12">
                <h6><i class="fa fa-plug" ></i>Datos de Conexión:</h6>
            </div>
        </div>
        <div class="row col-md-12">
            <div class="col-md-6">
                <div class="form-group row error-content">
                    <label for="id_empresa" class="col-md-2 col-form-label">Empresa:</label>
                    <div class="col-md-10">
                        <model-list-select
                            :disabled="cargando"
                            :onchange="changeSelect()"
                            name="id_empresa"
                            v-model="id_empresa"
                            option-value="id"
                            option-text="nombre"
                            :list="empresas"
                            :placeholder="!cargando?'Seleccionar o buscar empresa':'Cargando...'"
                            :isError="errors.has(`id_empresa`)">
                        </model-list-select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button @click="conectar" class="btn btn-primary float-right">
                    <i class="fa fa-plug"></i> Conectar
                </button>
            </div>
        </div>
        <span v-if="encontradas">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" />
                        </div>
                    </div>
                </div>
            </div>
        </span>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import Show from "./Show";
    import Edit from "./Edit";
    export default {
        name: "index-poliza",
        components: {Edit, ModelListSelect, Show},
        data() {
            return {
                cargando: false,
                conectando:false,
                conectado:false,
                buscando:false,
                encontradas:false,
                id_empresa: '',
                empresas: [],
                empresa_seleccionada: [],
                HeaderSettings: false,
                columns: [
                    { title: '#', field: 'index', sortable: false },
                    { title: 'Ejercicio', field: 'ejercicio', tdClass: 'td_fecha', thClass: 'th_fecha',  sortable: true },
                    { title: 'Periodo', field: 'periodo', tdClass: 'td_fecha', thClass: 'th_fecha',  sortable: true },
                    { title: 'Fecha', field: 'fecha', tdClass: 'td_fecha', thClass: 'th_fecha', sortable: true },
                    { title: 'Tipo', field: 'tipo', tdClass: 'td_fecha', thClass: 'th_fecha',  sortable: true },
                    { title: 'Folio', field: 'folio', tdClass: 'td_fecha', thClass: 'th_fecha',  sortable: true},
                    { title: 'Monto', field: 'monto', tdClass: 'td_money', thClass: 'th_money', sortable: true},
                    { title: 'Concepto', field: 'concepto', sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
            }
        },
        mounted(){
            this.getEmpresas();
        },
        computed: {
            polizas(){
                return this.$store.getters['contabilidadGeneral/poliza/polizas'];
            },
            poliza() {
                return this.$store.getters['contabilidadGeneral/poliza/currentPoliza'];
            },
            meta(){
                return this.$store.getters['contabilidadGeneral/poliza/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
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
                        tipo: poliza.tipo,
                        folio: poliza.folio,
                        monto: poliza.cargos,
                        concepto: poliza.concepto,
                        buttons: $.extend({}, {
                            id: poliza.id,
                            id_empresa: this.id_empresa,
                            editar:self.$root.can('editar_poliza',true) ? true : undefined,
                        })

                    }));
                },
                deep: true
            },
            poliza:{
                handler(poliza) {
                    if(poliza !== null){
                        this.tipo_modal = poliza.tipo_modal;
                    }else{
                        this.tipo_modal = '';
                    }
                }
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
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        },
        methods: {
            changeSelect(){
                this.conectando = false;
                var busqueda = this.empresas.find(x=>x.id === this.id_empresa);
                if(busqueda != undefined)
                {
                    this.empresa_seleccionada = busqueda;
                }
            },
            getPolizas(){
                this.query.id_empresa = this.id_empresa;
                this.query.ejercicio = this.ejercicio;
                this.query.periodo = this.periodo;
                this.query.tipo_poliza = this.tipo_poliza;
                this.query.numero_poliza = this.numero_poliza;
                this.query.texto = this.texto;
                this.buscando = true;
                this.$Progress.start();
                return this.$store.dispatch('contabilidadGeneral/poliza/paginate',
                    {
                        params: this.query
                    })
                    .then(data => {
                        this.encontradas = true;
                        this.$store.commit('contabilidadGeneral/poliza/SET_POLIZAS', data.data);
                        this.$store.commit('contabilidadGeneral/poliza/SET_META', data.meta);
                    }).finally(() => {
                        this.buscando = false;
                        this.$Progress.finish();
                    });

            },
            conectar(){
                this.conectando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa-contpaq/conectar',
                    {
                        data: {id: this.id_empresa},
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        if(this.empresa_seleccionada.alias_bdd === data){
                            this.conectado = true;
                            this.getPolizas();
                        }
                    }).finally(() => {
                        this.conectando = false;
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

</style>
