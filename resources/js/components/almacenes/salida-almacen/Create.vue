<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-md-12" >
                    <div class="invoice p-3 mb-3">
                     <form role="form" @submit.prevent="validate">
                        <div class="body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="fecha">Fecha:</label>
                                        <div class="col-sm-12">
                                                <datepicker v-model = "dato.fecha"
                                                            name = "fecha"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :disabled-dates="fechasDeshabilitadas"
                                                            :class="{'is-invalid': errors.has('fecha')}"
                                                ></datepicker>
                                          <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                <div class="col-md-10"></div>
                            </div>
                             <div class="row">
                                <!--Referencia-->
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia:</label>
                                        <div class="col-sm-10">
                                               <input class="form-control"
                                                      style="width: 100%"
                                                      placeholder="Referencia"
                                                      name="referencia"
                                                      id="referencia"
                                                      data-vv-as="Referencia"
                                                      v-validate="{required: true}"
                                                      v-model="dato.referencia"
                                                      :class="{'is-invalid': errors.has('referencia')}"
                                               >
                                          <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                <!--Almacen-->
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_almacen">Almacen:</label>
                                        <select
                                                :disabled="cargando"
                                                type="text"
                                                name="id_almacen"
                                                data-vv-as="Almacén"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="id_almacen"
                                                v-model="id_almacen"
                                               :class="{'is-invalid': errors.has('id_almacen')}"
                                        >
                                            <option value v-if="!cargando">- Seleccione -</option>
                                            <option value v-if="cargando">Cargando...</option>
                                            <option v-for="almacen in almacenes" :value="almacen.id">{{ almacen.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                    </div>
                                 </div>
                            </div>
                            <hr>
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="opciones" class="col-sm-3 col-form-label">Tipo: </label>
                                        <div class="col-sm-10">
                                            <div class="btn-group btn-group-toggle">
                                                <label class="btn btn-outline-secondary" :class="dato.opciones === Number(key) ? 'active': ''" v-for="(tipo, key) in tipos" :key="key">
                                                    <input type="radio"
                                                       class="btn-group-toggle"
                                                       name="opciones"
                                                       :id="'opciones' + key"
                                                       :value="key"
                                                       autocomplete="on"
                                                       v-validate="{required: true}"
                                                       @click="borrar"
                                                       v-model.number="dato.opciones">
                                                        {{ tipo }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <template v-if="dato.opciones == 1">
                                <div class="row">
                                    <div class="col-md-12" >
                                        <div class="form-group error-content">
                                        <label for="id_concepto">Concepto:</label>
                                            <concepto-select
                                                    name="id_concepto"
                                                    data-vv-as="Concepto"
                                                    v-validate="{required: true}"
                                                    id="id_concepto"
                                                    v-model="id_concepto"
                                                    :error="errors.has('id_concepto')"
                                                    ref="conceptoSelect"
                                                    :disableBranchNodes="false"
                                                    onselect="findConcepto"
                                            ></concepto-select>
                                        <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input button" id="con_prestamo" v-model="dato.con_prestamo" >
                                            <label class="custom-control-label" for="con_prestamo">Con préstamo a contratista</label>
                                        </div>
                                    </div>
                                        <div class="col-md-8" v-if="dato.con_prestamo">
                                            <select
                                                    class="form-control"
                                                    name="id_empresa"
                                                    data-vv-as="Empresa"
                                                    v-model="dato.id_empresa"
                                                    id="id_empresa"
                                                    :disabled="!dato.con_prestamo"
                                            >
                                                    <option v-if="dato.con_prestamo" value>-- Seleccione --</option>
                                                    <option value v-if="!dato.con_prestamo">-- No Aplica --</option>
                                                    <option v-for="(empresa, index) in empresas" :value="empresa.id"
                                                            data-toggle="tooltip" data-placement="left" :title="empresa.razon_social ">
                                                        {{ empresa.razon_social }}
                                                    </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2" v-if="dato.con_prestamo">
                                            <div class="btn-group btn-group-toggle">
                                                    <label class="btn btn-outline-primary" :class="dato.opcion_cargo === Number(key) ? 'active': ''" v-for="(cargo, key) in cargos" :key="key">
                                                        <input type="radio"
                                                               :disabled="!dato.con_prestamo"
                                                               class="btn-group-toggle "
                                                               name="opcion_cargo"
                                                               :id="'opcion_cargo' + key"
                                                               :value="key"

                                                               v-validate="{required: true}"
                                                               v-model.number="dato.opcion_cargo">
                                                            {{ cargo }}
                                                    </label>
                                                </div>
                                        </div>

                                </div>
                                <hr>

                            </template>


                            <div class="row">
                                <div class="col-md-12" v-if="id_almacen && ((dato.opciones == 1 && dato.id_concepto != '') || dato.opciones == 65537)">
                                    <div class="form-group">
                                        <div v-if="id_almacen">
                                             <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="bg-gray-light index_corto">#</th>
                                                            <th class="no_parte_input">No. de Parte</th>
                                                            <th>Material</th>
                                                            <th class="unidad">Unidad</th>
                                                            <th class="money_input">Existencia</th>
                                                            <th class="money_input">Cantidad</th>
                                                            <th class="icono"></th>
                                                            <th style="width: 200px; max-width: 200px; min-width: 200px">Destino</th>
                                                            <th style="width: 60px; max-width: 60px; min-width: 60px"></th>
                                                             <th class="bg-gray-light th_index">
                                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregar_partida" :disabled="cargando">
                                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                                <i class="fa fa-plus" v-else></i>
                                                            </button>
                                                        </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(partida, i) in dato.partidas">
                                                            <td>{{ i + 1}}</td>
                                                            <td>
                                                                <select

                                                                        :disabled = "!bandera"
                                                                        class="form-control"
                                                                        :name="`id_material[${i}]`"
                                                                        v-model="partida.material"
                                                                        v-validate="{required: true }"
                                                                        data-vv-as="No de Parte"
                                                                        :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                                >
                                                                     <option v-for="numero in materiales" :value="numero">{{ numero.numero_parte }}</option>
                                                                </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                            </td>
                                                            <td>
                                                                <select

                                                                        :disabled = "!bandera"
                                                                        class="form-control"
                                                                        :name="`id_material[${i}]`"
                                                                        v-model="partida.material"
                                                                        v-validate="{required: true }"
                                                                        data-vv-as="Descripción"
                                                                        :class="{'is-invalid': errors.has(`id_material[${i}]`)}"
                                                                >
                                                                 <option v-for="material in materiales" :value="material">{{ material.descripcion }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`id_material[${i}]`)">{{ errors.first(`id_material[${i}]`) }}
                                                            </div>
                                                            </td>
                                                            <td>
                                                                {{partida.material.unidad}}
                                                            </td>
                                                            <td class="money">
                                                                {{partida.material.saldo_almacen}}
                                                            </td>
                                                            <td>
                                                                <input
                                                                        :disabled = "!partida.material"
                                                                        type="number"
                                                                        step="any"
                                                                        :name="`cantidad[${i}]`"
                                                                        v-model="partida.material.cantidad"
                                                                        data-vv-as="Cantidad"
                                                                        v-validate="{required: true,min_value: 0.01, max_value:partida.material.saldo_almacen, decimal:2}"
                                                                        class="form-control"
                                                                        :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                        id="cantidad"
                                                                        placeholder="Cantidad">
                                                            <div class="invalid-feedback"
                                                                 v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}
                                                            </div>
                                                            </td>
                                                            <td  v-if="partida.destino ===  ''" >
                                                            <small class="badge badge-secondary">
                                                                <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                        </td>
                                                        <td v-else >
                                                            <small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >
                                                                <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                             <small class="badge badge-success" v-else="partida.destino.tipo_destino === 2" >
                                                                <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                            </small>
                                                        </td>
                                                        <td  v-if="partida.destino ===  ''" >
                                                        </td>
                                                        <td v-else >
                                                            <span v-if="partida.destino.tipo_destino === 1" style="text-decoration: underline"  :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>
                                                            <span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>
                                                        </td>
                                                            <td>
                                                                <i class="far fa-copy button" v-on:click="copiar_destino(partida)" ></i>
                                                                <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" ></i>
                                                            </td>
                                                            <td class="icono">
                                                                <button type="button" class="btn btn-outline-danger btn-sm" @click="borrarPartida(i)"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <div class="form-group error-content">
                                        <label for="observaciones">Observaciones:</label>
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="dato.observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="footer">
                           <button type="button" class="btn btn-secondary"  @click="index">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 || dato.partidas.length == 0">Guardar</button>
                        </div>
                     </form>
                    </div>
                </div>
            </div>
        </nav>
        <nav >
            <div class="modal fade" ref="modal_destino" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-destino"> <i class="fa fa-sign-in"></i> Seleccionar Destino</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form">
                            <div class="modal-body">
                                <div class="row" v-if="concepto.id>0 && concepto.id !==undefined && dato.opciones==1">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos:</label>
                                            <div class="col-sm-10">
                                                <ConceptoSelectHijo
                                                        name="id_conceptos"
                                                        data-vv-as="Concepto"
                                                        id="id_conceptos"
                                                        v-model="id_concepto_temporal"
                                                        ref="conceptoSelect"
                                                        :disableBranchNodes="true"
                                                        v-bind:nivel_id="concepto.id"
                                                ></ConceptoSelectHijo>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="dato.opciones==65537">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Activos:</label>
                                            <div class="col-sm-10">
                                                <select
                                                        name="id_almacen"
                                                        id="id_almacen_temporal"
                                                        data-vv-as="Almacén"
                                                        class="form-control"
                                                        v-model="almacen_temporal"
                                                >
                                                    <option value="">-- Almacén --</option>
                                                    <option v-for="almacen in almacenes" :value="almacen">{{ almacen.descripcion }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalDestino"><i class="fa fa-close"  ></i> Cerrar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>


    </span>
</template>

<script>
    import Almacen from "../../cadeco/almacen/Select";
    import ConceptoSelect from "../../cadeco/concepto/Select";
    import ConceptoSelectHijo from "../../cadeco/concepto/SelectHijo";
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';

    export default {
        name: "salida-almacen-create",
        components: {Almacen, ConceptoSelect,ConceptoSelectHijo,datepicker},
        data() {
            return {
                es:es,
                fechasDeshabilitadas:{},
                dato:{
                    con_prestamo: 0,
                    opcion_cargo: 1,
                    id_concepto:'',
                    fecha:'',
                    id_almacen:'',
                    id_empresa:'',
                    opciones:1,
                    referencia:'',
                    observaciones:'',
                    partidas:[]
                },
                tipos: {
                    1: "Consumo",
                    65537: "Transferencia"
                },
                cargos: {
                    1: "Con Cargo",
                    0: "Sin Cargo"
                },
                dato_partida:{
                    cantidad:'',
                    destino:''
                },
                contratista: {
                    empresa_contratista: '',
                    opcion:0
                },
                emp_cont:'',
                contratistas:[],
                partida:{},
                empresas:[],
                almacenes:[],
                materiales:[],
                indice:'',
                material:'',
                concepto:'',
                id_concepto:'',
                almacen:'',
                id_almacen:'',
                cargando: false,
                bandera : 0,
                index_temporal : '',
                almacen_temporal : '',
                id_concepto_temporal : '',
                destino_copiado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
                destino_seleccionado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
            }
        },
        init() {
            this.cargando = true;
        },
        mounted() {
            this.getAlmacenes();
            this.getEmpresas();
            this.getContratista();
            this.dato.fecha = new Date();
            this.fechasDeshabilitadas.from= new Date();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            agregar_partida(){
                var array = {
                    'material' : '',
                    'destino' : ''
                }
                if(this.materiales.length === 0 ) {
                    this.getMateriales();
                }

                this.dato.partidas.push(array);
                /*this.getMateriales();
                this.getAlmacenes();
                this.getContratista();
                this.cargando = true;
                this.contratista.empresa_contratista = '';
                this.contratista.opcion = 0;
                this.dato_partida.cantidad ='';
                this.dato_partida.destino ='';
                this.partida ={};
                $(this.$refs.modal).modal('show');
                this.$validator.reset();
                this.cargando = false;*/
            },
            agregarContratista(index){
                this.indice = index;
                if(this.dato.partidas[this.indice][4] == '' && this.dato.partidas[this.indice][5] == ''){
                    this.contratista.empresa_contratista = '';
                    this.contratista.opcion = 0;
                }else{
                    this.contratista.empresa_contratista = this.dato.partidas[this.indice][4].id;
                    this.contratista.opcion =  this.dato.partidas[this.indice][5];
                }
                this.getContratista().then(data =>{
                    this.cargando = true;
                    $(this.$refs.contratista).modal('show');
                    this.$validator.reset();
                    this.cargando = false;
                    this.emp_cont='';
                });
            },
            quitarContratista(){
                this.cargando = true;
                this.dato.partidas[this.indice][4] = '';
                this.dato.partidas[this.indice][5] = '';
                $(this.$refs.contratista).modal('hide');
                this.$validator.reset();
                this.emp_cont='';
                this.cargando = false;
            },
            modificarContratista(){
                this.cargando = true;
                this.findContratista().then(data => {
                    this.dato.partidas[this.indice][4] = this.emp_cont;
                    this.dato.partidas[this.indice][5] = this.contratista.opcion;
                    $(this.$refs.contratista).modal('hide');
                    this.$validator.reset();
                    this.cargando = false;
                    this.emp_cont='';
                });

            },
            getContratista() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'Contratista' }
                })
                    .then(data => {
                        this.contratistas = data.data;
                    })
            },
            getEmpresas() {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {sort: 'razon_social', order: 'asc', scope:'TipoContratista' }
                })
                    .then(data => {
                        this.empresas = data.data;
                    })
            },
            getMateriales() {
                this.materiales = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.id_almacen,
                    params: { include: 'materiales_salida' }
                })
                    .then(data => {
                        this.materiales = data.materiales_salida.data;
                        if( this.materiales.length != 0 ) {
                            this.bandera = 1;
                            this.cargando = false
                        }
                    })
                    .finally(() => {
                        if( this.materiales.length == 0 ) {
                            swal('Atención', 'No hay material disponible en este almacén.', 'warning');
                            this.bandera = 1;
                            this.cargando = false
                        }

                    })
            },
            findMaterial() {
                this.$store.commit('cadeco/material/SET_MATERIAL', null);
                return this.$store.dispatch('cadeco/material/find', {
                    id: this.partida[0],
                    params: {}
                }).then(data => {
                    this.material = data;
                })
            },
            getConcepto() {
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: this.destino_seleccionado.id_destino,
                    params: {
                    }
                })
                    .then(data => {
                        this.destino_seleccionado.destino = data;
                        this.seleccionarDestino();
                    })
            },
            findConcepto(value) {
                this.concepto = '';
                if(value !== undefined && value !== null){
                    this.$store.commit('cadeco/concepto/SET_CONCEPTO', null);
                    return this.$store.dispatch('cadeco/concepto/find', {
                        id: value,
                        params: {}
                    }).then(data => {
                        this.concepto = data;
                    });
                } else {
                    this.dato.partidas.forEach(function(partida) {
                        partida.destino = '';
                    });
                }
            },
            findContratista() {
                this.$store.commit('cadeco/empresa/SET_EMPRESA', null);
                return this.$store.dispatch('cadeco/empresa/find', {
                    id: this.contratista.empresa_contratista,
                    params: {}
                }).then(data => {
                    this.emp_cont = data;
                })
            },
            findAlmacen() {
                this.$store.commit('cadeco/almacen/SET_ALMACEN', null);
                if(this.dato.opciones == 65537){
                    return this.$store.dispatch('cadeco/almacen/find', {
                        id: this.dato_partida.destino,
                        params: {}
                    }).then(data => {
                        this.almacen = data;
                    })
                }else{
                    return this.$store.dispatch('cadeco/concepto/find', {
                        id: this.dato_partida.destino,
                        params: {}
                    }).then(data => {
                        this.almacen = data;
                    })
                }
            },
            getAlmacenes() {
                this.almacenes = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {
                        sort: 'descripcion',
                        order: 'asc'
                    }
                })
                    .then(data => {
                        this.almacenes = data.data;
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('almacenes/salida-almacen/store', this.dato)
                    .then((data) => {
                        this.$router.push({name: 'salida-almacen'});
                    });
            },
            borrar(){
                this.dato.partidas=[];
                this.dato.id_concepto='';
            },
            borrarPartida(i){
                this.dato.partidas.splice(i,1);
            },
            validarCantidad() {
                if(parseInt(this.partida[1]) < parseInt(this.dato_partida.cantidad)) {
                    swal('¡Error!', 'La cantidad no puede ser mayor a la existencia.', 'error');
                    this.dato_partida.cantidad = '';
                }else if( parseFloat(this.dato_partida.cantidad)<= 0){
                    swal('¡Error!', 'La cantidad no puede ser cero o menor.', 'error');
                    this.dato_partida.cantidad = '';
                }
            },
            validarAlmacen() {
                if(this.id_almacen == this.dato_partida.destino){
                    swal('¡Error!', 'No puede enviar la partida al mismo Almacén.', 'error');
                    this.dato_partida.destino='';
                }
            },
            index(){
                this.$router.push({name: 'salida-almacen'});
            },
            validatePartida() {
                this.findMaterial().finally(() => {
                    this.contratista.opcion = '';
                });
                this.findAlmacen().finally(() => {
                    this.dato.partidas.push([this.material, this.dato_partida.cantidad, this.almacen, this.partida, this.emp_cont, this.contratista.opcion]);
                });

                this.emp_cont='';
                $(this.$refs.modal).modal('hide');

            },
            modalDestino(i) {
                if(this.id_concepto == null || this.id_concepto == undefined)
                {
                    swal('Atención', 'Seleccione el concepto raíz', 'warning');
                }
                this.index_temporal = i;
                if(this.dato.partidas[this.index_temporal].destino == undefined || this.dato.partidas[this.index_temporal].destino == ''){
                    this.destino_seleccionado.tipo_destino =  '';
                    this.destino_seleccionado.destino = '';
                    this.destino_seleccionado.id_destino = '';
                }else {
                    this.destino_seleccionado = this.dato.partidas[this.index_temporal].destino;
                }
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            seleccionarDestino() {
                this.dato.partidas[this.index_temporal].destino = this.destino_seleccionado;
                this.index_temporal = '';
                this.destino_seleccionado = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                this.id_concepto_temporal = '';
                this.almacen_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            cerrarModalDestino(){
                this.id_concepto_temporal = '';
                this.almacen_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            copiar_destino(partida){
                this.destino_copiado = partida.destino;
            },
            pegar_destino(partida){
                partida.destino = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                partida.destino.id_destino = this.destino_copiado.id_destino;
                partida.destino.tipo_destino = this.destino_copiado.tipo_destino;
                partida.destino.destino = this.destino_copiado.destino;
            }
        },
        watch:{
            id_almacen(value){
                if(value != ''){
                    this.dato.id_almacen = value;
                    this.dato.partidas=[];
                    this.materiales = [];
                    this.bandera = 0;
                }
            },
            id_concepto(value){
                if(value != ''){
                    this.dato.id_concepto = value;
                    this.findConcepto(value);
                }
            },
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.almacen_temporal = '';
                    this.destino_seleccionado.id_destino = value;
                    this.destino_seleccionado.tipo_destino = 1;
                    this.getConcepto();
                }
            },
            almacen_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_concepto_temporal = '';
                    this.destino_seleccionado.id_destino = value.id;
                    this.destino_seleccionado.tipo_destino = 2;
                    this.destino_seleccionado.destino = value;
                    this.seleccionarDestino();
                }
            },
        }
    }
</script>
