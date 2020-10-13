<template>
    <span>
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
            <div class="col-md-2">
                <button @click="abrir" class="btn btn-primary float-right" :disabled="polizas.length == 0">
                    <i class="fa fa-download"></i> Descargar Formatos.
                </button>
            </div>
        </div>
        <span v-if="encontradas">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <button @click="descargarZIP" class="btn btn-primary float-right">
                            <i class="fa fa-file-excel-o"></i> Descarga Masiva ZIP
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <datatable v-bind="$data" />
                        </div>
                    </div>
                </div>
            </div>
        </span>
        <span>
            <!-- <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="PDFModal"> -->
                <div class="modal fade" ref="modalZip" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Descargar ZIP Polizas</h4>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                        </div>
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button @click="descargar(1)" type="button" class="btn btn-primary" title="Descargar ZIP">
                                        <i class="fa fa-file-pdf-o"></i>Descargar ZIP A
                                    </button>
                                    <button @click="descargar(2)" type="button" class="btn btn-primary" style="margin-left:5px" title="Descargar ZIP">
                                        <i class="fa fa-file-pdf-o"></i>Descargar ZIP B
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </span>
        <div class="modal fade" ref="modal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> Descarga Masiva ZIP</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <label for="carga_layout" class="col-lg-12 col-form-label">
                                Cargar Excel
                            </label>
                            <div class="col-lg-12">
                                 <input type="file" class="form-control" id="carga_layout"
                                        accept=".xlsx"
                                        @change="onFileChange"
                                        row="3"
                                        v-validate="{required: true, ext: ['xlsx','xls']}"
                                        name="carga_layout"
                                        data-vv-as="Layout"
                                        ref="carga_layout"
                                        :class="{'is-invalid': errors.has('carga_layout')}">
                                <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal()">Cerrar</button>
                         <button @click="validate" type="button" class="btn btn-primary" :disabled="errors.count() > 0">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
    
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "index-poliza",
        components: {ModelListSelect},
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
                    { title: 'Ejercicio', field: 'ejercicio', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default,  sortable: true },
                    { title: 'Periodo', field: 'periodo', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Fecha', field: 'fecha', tdClass: 'td_fecha', thClass: 'th_fecha', sortable: true },
                    { title: 'Tipo', field: 'tipo', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default, sortable: true },
                    { title: 'Folio', field: 'folio', tdClass: 'td_fecha', thClass: 'th_fecha', thComp: require('../../globals/th-Filter').default, sortable: true},
                    { title: 'Monto', field: 'monto', tdClass: 'td_money', thClass: 'th_money', sortable: true},
                    { title: 'Concepto', field: 'concepto',thComp: require('../../globals/th-Filter').default, sortable: false},
                    { title: 'Acciones', field: 'buttons',  tdComp: require('./partials/ActionButtons').default},
                ],
                data: [],
                total: 0,
                query: {},
                search: '',
                file:'',
                nombre: ''
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
            abrir(){
                $(this.$refs.modalZip).appendTo('body')
                $(this.$refs.modalZip).modal('show');
            },
            changeSelect(){
                this.conectando = false;
                var busqueda = this.empresas.find(x=>x.id === this.id_empresa);
                if(busqueda != undefined)
                {
                    this.empresa_seleccionada = busqueda;
                }
            },
            descargar(tipo){
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/poliza/descargaZip',
                    {
                        params: this.query,
                        tipo:tipo
                    })
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
            getPolizas(){
                this.query.id_empresa = this.id_empresa;
                this.buscando = true;
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
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            closeModal() {
                $(this.$refs.modal).modal('hide');
            },
            descargarZIP() {
                $(this.$refs.modal).modal('show');
            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.nombre = files[0].name;
                if(e.target.id == 'carga_layout') {
                    this.createImage(files[0]);
                }
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.cargaExcel()
                    }
                });
            },
            cargaExcel(){
                var formData = new FormData();
                formData.append('file',  this.file);
                formData.append('name', this.nombre);
                formData.append('id_empresa', this.id_empresa);
                return this.$store.dispatch('contabilidadGeneral/poliza/busquedaExcel', {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                }).then((data) => {
                    this.encontradas = true;
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZAS', data.data);
                    this.$store.commit('contabilidadGeneral/poliza/SET_META', data.meta);
                    $(this.$refs.modal).modal('hide');
                })
            },
        }
    }
</script>

<style scoped>

</style>
