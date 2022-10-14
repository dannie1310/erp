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
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <div class="form-group">
                                <label for="fecha_emision" >Fecha de Emisión</label>
                                <datepicker v-model = "fecha_emision"
                                            name = "fecha_emision"
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
                            <button type="button" class="btn btn-success btn-sm" @click="altaConceto()"><i class="fa fa-upload"></i>Alta de Concepto</button>
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
                                    <th class="c100">Importe</th>
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
                                                v-model="concepto.id_concepto"
                                                :class="{'is-invalid': errors.has(`concepto[${i}]`)}">
                                                <option value>--Seleccionar--</option>
                                                <option v-for="concept in tipoConceptos" :value="concept.id_concepto">{{ concept.nombre }}</option>
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
                            <button type="button" class="btn btn-success btn-sm" @click="altaPartida()"><i class="fa fa-upload"></i>Alta de Partida</button>
                            <button type="button" class="btn btn-success btn-sm" @click="agregarPartida()"><i class="fa fa-plus"></i>Agregar Partida</button>
                        </div>
                    </div>
                </div>
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
                                                class="form-control"
                                                :id="`partida[${i}]`"
                                                v-model="partida.id_partida"
                                                :class="{'is-invalid': errors.has(`partida[${i}]`)}">
                                                <option value>--Seleccionar--</option>
                                                <option v-for="par in tipos_partida" :value="par.id">{{ par.partida }} ({{par.nombre_operador}})</option>
                                            </select>
                                            <div class="invalid-feedback" v-show="errors.has(`partida[${i}]`)">{{ errors.first(`partida[${i}]`) }}</div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" :id="partida.antes_iva" v-on:keyup="importeTotalPartidas">
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
                                        <td>{{subtotal}}</td>
                                    </tr>
                                    <tr>
                                        <th>IVA</th>
                                        <td>{{iva}}</td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td>{{total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
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
            </div>
        </div>
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
                importe_partidas_antes : 0,
                importe_partidas_despues : 0,
                tipos_partida : {}
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
                id_concepto:'',
                importe: 0
            });
            this.getClientes();
            this.getConceptos();
            this.getEmpresas();
            this.getMonedas();
            this.getProyectos();
        },

        methods: {
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            agregarConcepto(){
                let temp_index = this.conceptos.length;
                this.conceptos.splice(temp_index, 0, {
                    id_concepto:'',
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
                this.importe_conceptos = importe
                this.totales();
            },
            totales(){
                this.subtotal = this.importe_conceptos - this.importe_partidas_antes;
                this.iva = this.subtotal * 0.16;
                this.total = (this.subtotal + this.iva) - this.importe_partidas_despues;
            },
            agregarPartida(){
                this.getPartidas()
                let temp_index = this.partidas.length;
                this.partidas.splice(temp_index, 0, {
                    id_partida : '',
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
                let importe_despues = 0;
                let importe_antes = 0;
                for(let i=0; i < this.partidas.length; i++) {
                    if(this.partidas[i].antes_iva) {
                        importe_despues += parseFloat(this.partidas[i].importe);
                    }else{
                        importe_antes += parseFloat(this.partidas[i].importe);
                    }
                }
                this.importe_partidas_antes = importe_antes;
                this.importe_partidas_despues = importe_despues;
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
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        /*if(moment(this.fecha_fin).format('YYYY/MM/DD') < moment(this.fecha_inicio).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else if(moment(this.fecha_hoy).format('YYYY/MM/DD') < moment(this.fecha).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha no puede ser mayor a la fecha actual.', 'error')
                        }
                        else {*/
                        this.store()
                        //}
                    }
                });
            },
            store() {
                return this.$store.dispatch('seguimiento/factura/store', {
                    id_antecedente: this.id,
                    fecha: moment(this.fecha).format('YYYY-MM-DD'),
                    cumplimiento: moment(this.fecha_inicio).format('YYYY-MM-DD'),
                    vencimiento:  moment(this.fecha_fin).format('YYYY-MM-DD'),
                    observaciones: this.observaciones,
                    conceptos: conceptos
                    fecha_emision:  moment(this.fecha_emision).format('YYYY-MM-DD'),
                    observaciones: '',

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
                    importe_partidas_antes : 0,
                    importe_partidas_despues : 0,
                    tipos_partida : {}
                }).then(data=> {
                    this.$router.push({name: 'estimacion-index'});
                    this.$router.push({name: 'estimacion'});
                })
            },
        },
        watch: {
            id_proyecto(value) {
                if(value !== '' && value !== null && value !== undefined){
                    if(value == 0)
                    {
                        swal('¡Error!', 'Seleccione un proyecto válido.', 'error');
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
