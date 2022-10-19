<template>
    <span>
        <nav>
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
            <div class="card" v-else>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_emision" >Fecha de Emisión</label>
                                    <datepicker v-model = "fecha_emision"
                                                name = "fecha_emision"
                                                v-on:keyup="getTipoCambio"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_emision')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_emision')">{{ errors.first('fecha_emision') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="numero_factura">Número de Factura:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Número de factura"
                                           name="numero_factura"
                                           id="numero_factura"
                                           data-vv-as="Número de Factura"
                                           v-validate="{required: true}"
                                           v-model="numero_factura"
                                           :class="{'is-invalid': errors.has('numero_factura')}">
                                    <div class="invalid-feedback" v-show="errors.has('numero_factura')">{{ errors.first('numero_factura') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_proyecto">Proyecto:</label>
                                    <select
                                        type="text"
                                        name="id_proyecto"
                                        data-vv-as="Proyecto"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_proyecto"
                                        v-model="id_proyecto"
                                        :class="{'is-invalid': errors.has('id_proyecto')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="proyecto in proyectos" :value="proyecto.id">{{ proyecto.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_empresa">Empresa:</label>
                                    <select
                                        type="text"
                                        name="id_empresa"
                                        data-vv-as="Empresa"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_empresa"
                                        v-model="id_empresa"
                                        :class="{'is-invalid': errors.has('id_empresa')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="empresa in empresas" :value="empresa.id">{{ empresa.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="descripcion">Descripción:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Descripción"
                                           name="descripcion"
                                           id="descripcion"
                                           data-vv-as="Descripción"
                                           v-validate="{required: true}"
                                           v-model="descripcion"
                                           :class="{'is-invalid': errors.has('descripcion')}">
                                    <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_cliente">Cliente:</label>
                                    <select
                                        type="text"
                                        name="id_cliente"
                                        data-vv-as="Cliente"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_cliente"
                                        v-model="id_cliente"
                                        :class="{'is-invalid': errors.has('id_cliente')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="cliente in clientes" :value="cliente.id">{{ cliente.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_cliente')">{{ errors.first('id_cliente') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_inicial" >Periodo que cubre:</label>
                                    <datepicker v-model = "fecha_inicial"
                                                name = "fecha_inicial"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_inicial')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="fecha_fin" >al</label>
                                    <datepicker v-model = "fecha_fin"
                                                name = "fecha_fin"
                                                :format = "formatoFecha"
                                                :language = "es"
                                                :bootstrap-styling = "true"
                                                class = "form-control"
                                                :disabled-dates="fechasDeshabilitadas"
                                                v-validate="{required: true}"
                                                :class="{'is-invalid': errors.has('fecha_fin')}"
                                    ></datepicker>
                                    <div class="invalid-feedback" v-show="errors.has('fecha_fin')">{{ errors.first('fecha_fin') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="id_moneda">Moneda:</label>
                                    <select
                                        type="text"
                                        name="id_moneda"
                                        data-vv-as="Moneda"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="id_moneda"
                                        v-model="id_moneda"
                                        :class="{'is-invalid': errors.has('id_moneda')}">
                                         <option value>--Seleccionar--</option>
                                        <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group error-content">
                                <div class="form-group">
                                    <label for="tipo_cambio">Tipo de Cambio:</label>
                                    <input class="form-control"
                                           style="width: 100%"
                                           placeholder="Tipo de Cambio"
                                           name="tipo_cambio"
                                           id="tipo_cambio"
                                           data-vv-as="Tipo de Cambio"
                                           v-validate="{required: true}"
                                           v-model="tipo_cambio"
                                           :class="{'is-invalid': errors.has('tipo_cambio')}">
                                    <div class="invalid-feedback" v-show="errors.has('tipo_cambio')">{{ errors.first('tipo_cambio') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-success btn-sm" @click="altaConcepto()"><i class="fa fa-upload"></i>Alta de Concepto</button>
                                <button type="button" class="btn btn-success btn-sm" @click="agregarConcepto()"><i class="fa fa-plus"></i>Agregar Concepto</button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Concepto</th>
                                        <th>Importe</th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(concepto, i) in conceptos">
                                            <td>{{i+1}}</td>
                                            <td>
                                                <select
                                                    type="text"
                                                    :name="`concepto[${i}]`"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    :id="`concepto[${i}]`"
                                                    v-model="concepto.idconcepto"
                                                    :class="{'is-invalid': errors.has(`concepto[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="concept in tipoConceptos" :value="concept.id">{{ concept.nombre }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`concepto[${i}]`)">{{ errors.first(`concepto[${i}]`) }}</div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                       :name="`importe[${i}]`"
                                                       data-vv-as="Importe"
                                                       step="any"
                                                       v-on:keyup="importeTotalConceptos"
                                                       v-model="concepto.importe"
                                                       v-validate="{required: true, decimal:2}"
                                                       :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                                       :id="`importe[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                            </td>
                                            <td class="icono">
                                                <button @click="eliminarConcepto(i)" :disabled="conceptos.length === 1" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                    <i class="fa fa-trash" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Importe:</th>
                                            <td>{{importe_conceptos}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                               <!-- <button type="button" class="btn btn-success btn-sm" @click="altaPartida()"><i class="fa fa-upload"></i>Alta de Partida</button> -->
                                <button type="button" class="btn btn-success btn-sm" @click="agregarPartida()"><i class="fa fa-plus"></i>Agregar Partida</button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row" v-if="partidas.length != 0">
                        <br />
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto">#</th>
                                        <th>Partida</th>
                                        <th></th>
                                        <th class="c150">Importe</th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(partida, i) in partidas">
                                            <td>{{i+1}}</td>
                                            <td>
                                                <select
                                                    type="text"
                                                    :name="`partida[${i}]`"
                                                    data-vv-as="Partida"
                                                    v-validate="{required: true}"
                                                    v-on:change="getPartida(i)"
                                                    class="form-control"
                                                    :id="`partida[${i}]`"
                                                    v-model="partida.idpartida"
                                                    :class="{'is-invalid': errors.has(`partida[${i}]`)}">
                                                    <option value>--Seleccionar--</option>
                                                    <option v-for="par in tipos_partida" :value="par.id">{{ par.partida }} ({{par.nombre_operador}})</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`partida[${i}]`)">{{ errors.first(`partida[${i}]`) }}</div>
                                            </td>
                                            <td>
                                                 <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" :id="partida.antes_iva" v-model="partida.antes_iva" :disabled="partida.idpartida == ''" v-on:click="importeTotalPartidas">
                                                    <label class="form-check-label" for="antes_iva">¿Antes de IVA?</label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                       :name="`importe_partida[${i}]`"
                                                       data-vv-as="Importe"
                                                       step="any"
                                                       v-on:keyup="importeTotalPartidas"
                                                       v-model="partida.importe"
                                                       v-validate="{required: true, decimal:2}"
                                                       :class="{'is-invalid': errors.has(`importe_partida[${i}]`)}"
                                                       :id="`importe_partida[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`importe_partida[${i}]`)">{{ errors.first(`importe_partida[${i}]`) }}</div>
                                            </td>
                                            <td class="icono">
                                                <button @click="eliminarPartida(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                    <i class="fa fa-trash" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td>{{parseFloat(subtotal).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>IVA</th>
                                            <td>{{parseFloat(iva).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <th>Total:</th>
                                            <td>{{parseFloat(total).formatMoney(2,'.',',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar</button>
                        <button type="button" @click="validate" :disabled="errors.count() > 0" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <nav>
            <div class="modal fade" ref="modal_concepto" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal_concepto"> <i class="fa fa-sign-in"></i> Alta de Concepto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group error-content">
                                            <div class="form-group">
                                                <label for="nuevo_concepto">Concepto:</label>
                                                <input class="form-control"
                                                       style="width: 100%"
                                                       placeholder="Concepto"
                                                       name="nuevo_concepto"
                                                       id="nuevo_concepto"
                                                       data-vv-as="Nuevo Concepto"
                                                       v-validate="{required: true}"
                                                       v-model="nuevo_concepto"
                                                       :class="{'is-invalid': errors.has('nuevo_concepto')}">
                                                <div class="invalid-feedback" v-show="errors.has('nuevo_concepto')">{{ errors.first('nuevo_concepto') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="pull-right">
                                    <button type="button" class="btn btn-secondary" v-on:click="cerrarModalConcepto">
                                        <i class="fa fa-close"></i>
                                        Cerrar
                                    </button>
                                    <button type="button" @click="guardarConcepto" :disabled="nuevo_concepto != ''" class="btn btn-primary">
                                        <i class="fa fa-save"></i>
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "create",
        components: {Datepicker, es},
        data() {
            return {
                es: es,
                cargando: false,
                fecha_emision: '',
                observaciones: '',
                fecha: '',
                fechasDeshabilitadas:{},
                fecha_hoy : '',
                numero_factura : '',
                descripcion : '',
                proyectos : {},
                id_proyecto : '',
                empresas : {},
                id_empresa : '',
                clientes : {},
                id_cliente : '',
                fecha_inicial : '',
                fecha_fin : '',
                monedas : {},
                id_moneda : '',
                tipo_cambio : '',
                tipoConceptos : {},
                conceptos : [],
                importe_conceptos : 0,
                subtotal : 0,
                iva : 0,
                total : 0,
                partidas : [],
                importe_partidas_antes_suma : 0,
                importe_partidas_antes_resta: 0,
                importe_partidas_despues_suma : 0,
                importe_partidas_despues_resta : 0,
                tipos_partida : {},
                nuevo_concepto : '',
                tipos_cambios : [],
            }
        },
        mounted() {
            this.fecha_emision = new Date()
            this.fecha = new Date()
            this.fecha_hoy = new Date()
            this.fechasDeshabilitadas.from= new Date();
            this.fecha_inicial = new Date();
            this.fecha_fin = new Date();
            this.conceptos.push({
                idconcepto:'',
                importe: 0
            });
            this.getClientes();
            this.getConceptos();
            this.getEmpresas();
            this.getMonedas();
            this.getProyectos();
            this.getTipoCambio();
        },

        methods: {
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            agregarConcepto(){
                let temp_index = this.conceptos.length;
                this.conceptos.splice(temp_index, 0, {
                    idconcepto:'',
                    importe:0.00
                });
            },
            eliminarConcepto(index){
                let temp_index = index - 1;
                while(temp_index in this.conceptos){
                    temp_index= temp_index - 1;
                }
                this.conceptos.splice(index, 1);
                this.importeTotalConceptos()
            },
            importeTotalConceptos() {
                let importe = 0;
                for(let i=0; i < this.conceptos.length; i++) {
                    importe += parseFloat(this.conceptos[i].importe);
                }
                this.importe_conceptos = parseFloat(importe).formatMoney(2,'.','')
                this.totales();
            },
            totales(){
                this.subtotal = this.importe_conceptos
                this.subtotal = this.subtotal + this.importe_partidas_antes_suma;
                this.subtotal = this.subtotal - this.importe_partidas_antes_resta;
                this.iva = this.subtotal * 0.16;
                this.total = (this.subtotal + this.iva)
                this.total = this.total + this.importe_partidas_despues_suma;
                this.total = this.total - this.importe_partidas_despues_resta;
            },
            agregarPartida(){
                this.getPartidas()
                let temp_index = this.partidas.length;
                this.partidas.splice(temp_index, 0, {
                    idpartida : '',
                    antes_iva : false,
                    importe : 0
                });
            },
            eliminarPartida(index){
                let temp_index = index - 1;
                while(temp_index in this.partidas){
                    temp_index= temp_index - 1;
                }
                this.partidas.splice(index, 1);
                this.importeTotalPartidas()
            },
            importeTotalPartidas() {
                let importe_despues_resta = 0;
                let importe_despues_suma = 0;
                let importe_antes_resta = 0;
                let importe_antes_suma = 0;

                for(let i=0; i < this.partidas.length; i++) {
                    if (this.partidas[i].nombre_operador == 'MENOS') {
                        if (this.partidas[i].antes_iva === false) {
                            importe_antes_resta += parseFloat(this.partidas[i].importe);
                        }
                        if (this.partidas[i].antes_iva === true) {
                            importe_despues_resta += parseFloat(this.partidas[i].importe);
                        }
                    }
                    if (this.partidas[i].nombre_operador == 'MAS') {
                        if (this.partidas[i].antes_iva === false) {
                            importe_antes_suma += parseFloat(this.partidas[i].importe);
                        }
                        if (this.partidas[i].antes_iva === true) {
                            importe_despues_suma += parseFloat(this.partidas[i].importe);
                        }
                    }
                }
                this.importe_partidas_antes_suma = importe_antes_suma;
                this.importe_partidas_antes_resta = importe_antes_resta;
                this.importe_partidas_despues_suma = importe_despues_suma;
                this.importe_partidas_despues_resta = importe_despues_resta;
                this.totales();
            },
            getClientes() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/cliente/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'cliente'
                    }
                })
                    .then(data => {
                        this.clientes = data.data;
                    })
            },
            getConceptos() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/tipo-ingreso/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'tipo_ingreso'
                    }
                })
                    .then(data => {
                        this.tipoConceptos = data.data;
                    })
            },
            getEmpresas() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/empresa/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'empresa'
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getMonedas() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/moneda/index', {
                    params: {
                        order: 'ASC',
                        sort: 'orden'
                    }
                })
                    .then(data => {
                        this.monedas = data.data;
                        this.id_moneda = 3
                        this.tipo_cambio = 1.00
                    })
            },
            getProyectos() {
                this.cargando = true;
                return this.$store.dispatch('seguimiento/proyecto/index', {
                    params: {
                        scope: ['porTipo'],
                        order: 'ASC',
                        sort: 'proyecto'
                    }
                }).then(data => {
                    this.proyectos = data;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            getPartidas() {
                return this.$store.dispatch('seguimiento/ingreso-partida/index', {
                    params: {
                        scope: ['activos'],
                        order: 'ASC',
                        sort: 'partida'
                    }
                })
                    .then(data => {
                        this.tipos_partida = data.data;
                    })
            },
            getPartida(i) {
                 return this.$store.dispatch('seguimiento/ingreso-partida/find', {
                    id: this.partidas[i]['idpartida'],
                    params: {}
                }).then(data => {
                     this.partidas[i]['nombre_operador'] = data['nombre_operador'];
                     this.importeTotalPartidas();
                })
            },

            getTipoCambio() {
                return this.$store.dispatch('igh/tipo-cambio/index', {
                    params: {
                        scope: ['porFecha:'+moment(this.fecha).format('YYYY-MM-DD')]
                    }
                })
                    .then(data => {
                        this.tipos_cambios = data;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(moment(this.fecha_fin).format('YYYY/MM/DD') < moment(this.fecha_inicial).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'El periodo de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('seguimiento/factura/store', {
                    fi_cubre: moment(this.fecha_inicial).format('YYYY-MM-DD'),
                    ff_cubre:  moment(this.fecha_fin).format('YYYY-MM-DD'),
                    conceptos: this.conceptos,
                    fecha:  moment(this.fecha_emision).format('YYYY-MM-DD'),
                    numero : this.numero_factura,
                    descripcion : this.descripcion,
                    idproyecto : this.id_proyecto,
                    idempresa : this.id_empresa,
                    idcliente : this.id_cliente,
                    idmoneda : this.id_moneda,
                    tipo_cambio : this.tipo_cambio,
                    importe_conceptos : this.importe_conceptos,
                    importe : this.subtotal,
                    iva : this.iva,
                    total : this.total,
                    partidas : this.partidas,
                    importe_partidas_antes : this.importe_partidas_antes,
                    importe_partidas_despues : this.importe_partidas_despues,
                }).then(data=> {
                    //this.salir();
                })
            },
            salir()
            {
                this.$router.go(-1);
            },
            altaConcepto()
            {
                this.nuevo_concepto =  '';
                this.$validator.reset();
                $(this.$refs.modal_concepto).modal('show');
            },
            cerrarModalConcepto(){
                this.nuevo_concepto = '';
                $(this.$refs.modal_concepto).modal('hide');
                this.$validator.reset();
            },
            guardarConcepto() {
                return this.$store.dispatch('seguimiento/tipo-ingreso/store', {
                    tipo_ingreso: this.nuevo_concepto,
                }).then(data => {
                    this.cerrarModalConcepto();
                    this.getConceptos();
                })
            }
        },
        watch: {
            id_proyecto(value) {
                if(value !== '' && value !== null && value !== undefined){
                    if(value == 0)
                    {
                        swal('¡Error!', 'Seleccione un proyecto válido.', 'error');
                    }
                }
            },
            id_moneda(value) {
                if(value !== '' && value !== null && value !== undefined){
                    if(value != 3 && value != 4) {
                        for (let i = 0; i < this.tipos_cambios.length; i++)
                        {
                            if (value == this.tipos_cambios[i]['moneda']) {
                                this.tipo_cambio = this.tipos_cambios[i]['tipo_cambio'];
                            }
                        }
                    }
                    if(value == 4)
                    {
                        this.tipo_cambio = 0;
                    }
                }
            },
        }
    }
</script>

<style scoped>

</style>
