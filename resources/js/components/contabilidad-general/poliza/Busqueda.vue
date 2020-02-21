<template>
    <span>
        <div class="row">
            <div class="col-12">
                <show></show>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h6><i class="fa fa-plug" ></i>Datos de Conexión:</h6>
            </div>
        </div>
         <div class="row">
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
                                 option-text="descripcion"
                                 :list="empresas"
                                 :placeholder="!cargando?'Seleccionar o buscar empresa':'Cargando...'"
                                 :isError="errors.has(`id_empresa`)">
                         </model-list-select>
                     </div>
                 </div>
             </div>
             <div class="col-md-1">
                 <button @click="conectar" class="btn btn-primary float-right">
                        <i class="fa fa-plug"></i> Conectar
                 </button>
             </div>
         </div>
        <span v-if="conectado">
            <hr />
            <div class="row">
                <div class="col-md-12">
                    <h6><i class="fa fa-filter" ></i>Parámetros de búsqueda:</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                     <div class="form-group row error-content">
                         <label for="ejercicio" class="col-md-6 col-form-label">Ejercicio:</label>
                         <div class="col-md-6">
                             <input
                                     type="text"
                                     name="ejercicio"
                                     data-vv-as="ejercicio"
                                     class="form-control"
                                     id="ejercicio"
                                     placeholder="Año"
                                     v-model="ejercicio"
                                     :class="{'is-invalid': errors.has('ejercicio')}">
                             <div class="invalid-feedback" v-show="errors.has('ejercicio')">{{ errors.first('ejercicio') }}</div>

                         </div>
                     </div>
                 </div>
                <div class="col-md-2">
                     <div class="form-group row error-content">
                         <label for="periodo" class="col-md-6 col-form-label">Periodo:</label>
                         <div class="col-md-6">
                             <input
                                     type="text"
                                     name="periodo"
                                     data-vv-as="Periodo"
                                     class="form-control"
                                     id="periodo"
                                     placeholder="Periodo"
                                     v-model="periodo"
                                     :class="{'is-invalid': errors.has('periodo')}">
                             <div class="invalid-feedback" v-show="errors.has('periodo')">{{ errors.first('periodo') }}</div>

                         </div>
                     </div>
                 </div>
                <div class="col-md-2">
                     <div class="form-group row error-content">
                         <label for="tipo_poliza" class="col-md-6 col-form-label">Tipo de Poliza:</label>
                         <div class="col-md-6">
                             <input
                                     type="text"
                                     name="tipo_poliza"
                                     data-vv-as="Tipo de Póliza"
                                     class="form-control"
                                     id="tipo_poliza"
                                     placeholder="I, E, D"
                                     v-model="tipo_poliza"
                                     :class="{'is-invalid': errors.has('tipo_poliza')}">
                             <div class="invalid-feedback" v-show="errors.has('tipo_poliza')">{{ errors.first('tipo_poliza') }}</div>

                         </div>
                     </div>
                 </div>
                 <div class="col-md-2">
                     <div class="form-group row error-content">
                         <label for="numero_poliza" class="col-md-6 col-form-label"># Poliza:</label>
                         <div class="col-md-6">
                             <input
                                     type="text"
                                     name="numero_poliza"
                                     data-vv-as="Número de Póliza"
                                     class="form-control"
                                     id="numero_poliza"
                                     placeholder="# Póliza"
                                     v-model="numero_poliza"
                                     :class="{'is-invalid': errors.has('numero_poliza')}">
                             <div class="invalid-feedback" v-show="errors.has('numero_poliza')">{{ errors.first('numero_poliza') }}</div>

                         </div>
                     </div>
                 </div>
                <div class="col-md-2">
                     <div class="form-group row error-content">
                         <label for="texto" class="col-md-6 col-form-label">Texto:</label>
                         <div class="col-md-6">
                             <input
                                     type="text"
                                     name="texto"
                                     data-vv-as="Texto"
                                     class="form-control"
                                     id="texto"
                                     placeholder="Texto"
                                     v-model="texto"
                                     :class="{'is-invalid': errors.has('texto')}">
                             <div class="invalid-feedback" v-show="errors.has('texto')">{{ errors.first('texto') }}</div>

                         </div>
                     </div>
                 </div>
                <div class="col-md-1">
                    <button @click="getPolizas" v-if="$root.can('editar_poliza',true)" class="btn btn-primary float-right">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
             </div>
        </span>
        <span v-if="encontradas">
            <hr />
            <div class="row">
            <div class="col-md-12">
                <h6><i class="fa fa-th-list" ></i>Resultados de la búsqueda:</h6>
            </div>
        </div>
            <div >
                <datatable v-bind="$data" />
            </div>
        </span>

    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import Show from "./Show";
    export default {
        name: "busqueda-poliza",
        components: {ModelListSelect, Show},
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
                numero_poliza : '',
                periodo: '',
                ejercicio: '',
                tipo_poliza: '',
                texto:'',

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
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        },
        methods: {
            tipos_polizas(){
                return {
                    1: "Ingresos",
                    2: "Egresos",
                    3: "Diario"
                };
            },
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
                return this.$store.dispatch('contabilidadGeneral/empresa/conectar',
                    {
                        data: {id: this.id_empresa},
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        if(this.empresa_seleccionada.alias_bdd === data){
                            this.conectado = true;
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
                        order: 'asc'
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