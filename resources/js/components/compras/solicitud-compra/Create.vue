<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "fecha"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :disabled-dates="fechasDeshabilitadas"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            />
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-between">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_area_compradora">Departamento Responsable:</label>
                                            <select class="form-control"
                                                    name="id_area_compradora"
                                                    data-vv-as="Departamento Responsable"
                                                    v-model="id_area_compradora"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_compradora')"
                                                    id="id_area_compradora">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="area in areas_compradoras" :value="area.id" >{{ area.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_compradora')">{{ errors.first('id_area_compradora') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_tipo">Tipo:</label>
                                            <select class="form-control"
                                                    data-vv-as="Tipo"
                                                    id="id_tipo"
                                                    name="id_tipo"
                                                    :error="errors.has('id_tipo')"
                                                    v-validate="{required: true}"
                                                    v-model="id_tipo">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(tipo, index) in tipos" :value="tipo.id" >{{ tipo.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_tipo')">{{ errors.first('id_tipo') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" v-if="configuracion && configuracion.configuracion_area_solicitante == 1">
                                        <div class="form-group">
                                            <label for="id_area_solicitante">Área Solicitante:</label>
                                            <select class="form-control"
                                                    id="id_area_solicitante"
                                                    data-vv-as="Área Solicitante"
                                                    name="id_area_solicitante"
                                                    v-model="id_area_solicitante"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_solicitante')">
                                                <option value>-- Seleccionar --</option>
                                                <option v-for="area_s in areas_solicitantes" :value="area_s.id" >{{ area_s.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_solicitante')">{{ errors.first('id_area_solicitante') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha de la Requisición Origen:</label>
                                            <datepicker v-model = "fecha_requisicion"
                                                        name = "fecha_requisicion"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha_requisicion')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha_requisicion')">{{ errors.first('fecha_requisicion') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Folio de la Requisición Origen:</label>
                                            <input
                                                type="number"
                                                data-vv-as="Folio Requisición"
                                                name = "folio_requisicion"
                                                id="folio_requisicion"
                                                class="form-control"
                                                placeholder="Folio Requisición"
                                                v-validate="{min_value:0, max_value: 999999999}"
                                                :class="{'is-invalid': errors.has('folio_requisicion')}"
                                                v-model="folio_req"/>
                                                <div class="invalid-feedback" v-show="errors.has('folio_requisicion')">{{ errors.first('folio_requisicion') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="concepto" class="col-form-label">Concepto: </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                                <textarea
                                                    name="concepto"
                                                    id="concepto"
                                                    class="form-control"
                                                    v-model="concepto"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Concepto"
                                                    :class="{'is-invalid': errors.has('concepto')}"
                                                ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div  class="col-md-12 table-responsive-xl">
                                        <div>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th class="index_corto">#</th>
                                                    <th style="width:100px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="cantidad_input">Cantidad</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th style="width:140px;">Fecha Entrega</th>
                                                    <th>Destino</th>
                                                    <th class="icono"></th>
                                                    <th>Observaciones</th>
                                                    <th class="icono">
                                                        <button type="button" class="btn btn-success btn-sm" v-if="cargando"  title="Cargando..." :disabled="cargando">
                                                            <i class="fa fa-spin fa-spinner"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-success btn-sm" @click="addPartidas()" v-else>
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(partida, i) in partidas">
                                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                    <td v-if="partida.material === ''">
                                                    </td>
                                                    <td v-else>{{partida.material.numero_parte}}</td>
                                                    <td v-if="partida.material === ''">
                                                        <MaterialSelect
                                                            :id="`id_material_${i}`"
                                                            :name="`material[${i}]`"
                                                            :scope="scope"
                                                            sort = "descripcion"
                                                            v-model="partida.material"
                                                            data-vv-as="Material"
                                                            v-validate="{required: true}"
                                                            :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                            :class="{'is-invalid': errors.has(`material[${i}]`)}"
                                                            :ref="`MaterialSelect_${i}`"
                                                            :disableBranchNodes="false"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`material[${i}]`)">{{ errors.first(`material[${i}]`) }}</div>
                                                    </td>
                                                    <td v-else>{{partida.material.descripcion}}</td>
                                                    <td>
                                                        <input type="number"
                                                               min="0.001"
                                                               step=".001"
                                                               class="form-control"
                                                               :name="`cantidad[${i}]`"
                                                               data-vv-as="Cantidad"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                               v-model="partida.cantidad"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                    </td>
                                                    <td style="width:70px;" v-if="partida.material">{{partida.material.unidad}}</td>
                                                    <td style="width:120px;" v-else></td>
                                                    <td class="fecha">
                                                        <datepicker v-model="partida.fecha"
                                                                    :name="`fecha[${i}]`"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    :disabled-dates="fechasEntregaDeshabilitadas"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                        ></datepicker>
                                                        <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                    </td>
                                                    <td  v-if="partida.destino ===  ''" >
                                                         <small class="badge badge-secondary">
                                                            <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                    </td>
                                                    <td v-else>
                                                        <small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >
                                                            <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <small class="badge badge-success" v-else="partida.destino.tipo_destino === 2" >
                                                            <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <span v-if="partida.destino.tipo_destino === 1" style="text-decoration: underline"  :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>
                                                        <span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>
                                                    </td>
                                                    <td class="icono">
                                                        <i class="far fa-copy button" v-on:click="copiar_destino(partida)" title="Copiar" ></i>
                                                        <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" title="Pegar"></i>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  data-vv-as="Observaciones"
                                                                  v-validate="{}"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="partida.observaciones"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                    </td>
                                                    <td>
                                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="observaciones" class="col-form-label">Observaciones: </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row error-content">
                                                <textarea
                                                    name="observaciones"
                                                    id="observaciones"
                                                    class="form-control"
                                                    v-model="observaciones"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Observaciones"
                                                    :class="{'is-invalid': errors.has('observaciones')}"
                                                ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" v-on:click="salir">
                                    <i class="fa fa-angle-left"></i>
                                    Regresar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="cargando">
                                     <span v-if="cargando">
                                         <i class="fa fa-spin fa-spinner"></i>
                                     </span>
                                    <span v-else>
                                        <i class="fa fa-save"></i> Guardar
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        <nav>
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
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos:</label>
                                            <div class="col-sm-10">
                                                <concepto-select
                                                    name="id_concepto"
                                                    data-vv-as="Concepto"
                                                    id="id_concepto"
                                                    v-model="id_concepto_temporal"
                                                    :error="errors.has('id_concepto')"
                                                    ref="conceptoSelect"
                                                    :disableBranchNodes="true"
                                                ></concepto-select>
                                                <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row error-content">
                                            <label for="id_almacen" class="col-sm-2 col-form-label">Activos:</label>
                                            <div class="col-sm-10">
                                                <model-list-select
                                                    name="id_almacen"
                                                    placeholder="Seleccionar o buscar descripción del almacén"
                                                    data-vv-as="Almacén"
                                                    v-model="id_almacen_temporal"
                                                    option-value="id"
                                                    option-text="descripcion"
                                                    :list="almacenes"
                                                >
                                                </model-list-select>
                                                <div class="invalid-feedback" v-show="errors.has('id_almacen')">{{ errors.first('id_almacen') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalDestino">
                                    <i class="fa fa-close"  ></i> Cerrar
                                </button>
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
    import {ModelListSelect} from 'vue-search-select';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    import MaterialSelect from '../../cadeco/material/SelectAutocomplete';
    export default {
        name: "solicitud-compra-create",
        components: {Datepicker, ModelListSelect, es,ConceptoSelect, MaterialSelect},
        data(){
            return{
                scope: ['materialesParaCompras'],
                cargando: false,
                es:es,
                configuracion : '',
                fechasDeshabilitadas:{},
                fechasEntregaDeshabilitadas:{},
                fecha : '',
                fecha_requisicion : '',
                fecha_hoy : '',
                id_material: '',
                areas_compradoras : [],
                areas_solicitantes : [],
                tipos : [],
                id_area_compradora : '',
                index_temporal : '',
                id_almacen_temporal : '',
                id_concepto_temporal : '',
                id_tipo : '',
                materiales : [],
                almacenes : [],
                id_area_solicitante : null,
                concepto : '',
                observaciones : '',
                folio_req : '',
                destino_seleccionado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
                destino_copiado: {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                },
                partidas: [
                    {
                        i : 0,
                        material : "",
                        unidad : "",
                        numero_parte : "",
                        descripcion : "",
                        cantidad : "",
                        fecha : "",
                        observaciones : "",
                        concepto_temporal : "",
                        destino :  ""
                    }
                ],
            }
        },
        mounted() {
            this.$validator.reset()
            this.fecha_hoy = new Date();
            this.fecha_requisicion = new Date();
            this.fecha = new Date();
            this.fechasEntregaDeshabilitadas.to = new Date();
            this.getAreasCompradoras();
            this.getConfiguracion();
            this.getTipos();
            // this.getMateriales();
            this.getAlmacenes();
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.cargando = true;
                this.areas_compradoras = '';
                this.areas_solicitantes = [];
                this.tipos = [];
                this.id_area_compradora = '';
                this.id_tipo = '';
                this.id_area_solicitante = '';
                this.concepto = '';
                this.observaciones = '';
                this.id_concepto_temporal = '';
                this.partidas = [{
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                    concepto_temporal : "",
                    destino :  ""
                }];
            },
            getConfiguracion() {
                return this.$store.dispatch('seguridad/configuracion-obra/getConfiguracion', {  } )
                    .then(data => {
                       this.configuracion =  data;
                       if(data.configuracion_area_solicitante == 1) {
                           this.getAreasSolicitantes();
                           this.id_area_solicitante = '';
                       }
                    })
            },
            changeSelect(item){
                // var busqueda = this.materiales.find(x=>x.id === item.id_material);
                // if(busqueda != undefined)
                // {
                //     item.material = busqueda;
                // }
            },
            idAndNumeroParteAndDescripcion (item) {
                return `[${item.id}] - [${item.numero_parte}] -  ${item.descripcion}`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreasCompradoras() {
                return this.$store.dispatch('configuracion/area-compradora/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_compradoras = data;
                        this.disabled = false;
                    })
            },
            getTipos() {
                return this.$store.dispatch('configuracion/ctg-tipo/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.tipos = data.data;
                        this.disabled = false;
                    })
            },
            modalDestino(i) {
                this.index_temporal = i;
                if(this.partidas[this.index_temporal].destino == undefined || this.partidas[this.index_temporal].destino == ''){
                    this.destino_seleccionado.tipo_destino =  '';
                    this.destino_seleccionado.destino = '';
                    this.destino_seleccionado.id_destino = '';
                }else {
                    this.destino_seleccionado = this.partidas[this.index_temporal].destino;
                }

                if(this.almacenes.length == 0) {
                    this.getAlmacenes().finally(()=>{this.cargando = false});
                }
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            seleccionarDestino() {
                this.partidas[this.index_temporal].destino = this.destino_seleccionado;
                this.index_temporal = '';
                this.destino_seleccionado = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                this.id_concepto_temporal = '';
                this.id_almacen_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            cerrarModalDestino(){
                this.id_concepto_temporal = '';
                this.id_almacen_temporal = '';
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
            getAlmacenes() {
                this.$store.commit('cadeco/almacen/SET_ALMACENES', []);
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion', order: 'asc', }
                })
                    .then(data => {
                        this.almacenes = data.data
                        this.cargando = false;
                    });
            },
            getAlmacen() {
                return this.$store.dispatch('cadeco/almacen/find', {
                    id: this.destino_seleccionado.id_destino,
                    params: {
                    }
                })
                    .then(data => {
                        this.destino_seleccionado.destino = data;
                        this.seleccionarDestino();
                    })
            },
            getAreasSolicitantes() {
                return this.$store.dispatch('configuracion/area-solicitante/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_solicitantes = data;
                        this.disabled = false;
                    })
            },
            addPartidas(){
                this.partidas.splice(this.partidas.length + 1, 0, {
                    material : "",
                    descripcion : "",
                    unidad : "",
                    numero_parte : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                    concepto_temporal : "",
                    destino :  ""
                });
                this.index = this.index+1;
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            destroy(index){
                this.partidas.splice(index, 1);
            },
            getMateriales() {
                this.materiales = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: 'materialesParaCompras',
                        sort: 'descripcion', order: 'asc', limit:100
                    }
                })
                    .then(data => {
                        this.materiales = data.data;
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        var t = 0;
                        var m = 0;
                        while(t < this.partidas.length){
                            this.partidas[t].id_material = this.partidas[t].material.id;
                            if(this.partidas[t].destino === '' || typeof this.partidas[t].destino.destino === 'undefined' )
                            {
                                m ++;
                                swal('¡Error!', 'Ingrese un destino válido en partida '+(t + 1) +'.', 'error');
                            }
                            else if(moment(this.fecha).format('YYYY/MM/DD') > moment(this.partidas[t].fecha).format('YYYY/MM/DD'))
                            {
                                m ++;
                                swal('¡Error!', 'La fecha de la partida '+(t + 1) +' ('+moment(this.partidas[t].fecha).format('DD/MM/YYYY')+') debe ser posterior o igual a la fecha de la solicitud ('+moment(this.fecha).format('DD/MM/YYYY')+').', 'error')
                            }
                            t ++;
                        }
                        if(m == 0) {
                            this.store()
                        }
                    }
                });
            },
            store() {
                return this.$store.dispatch('compras/solicitud-compra/store', {
                    concepto: this.concepto,
                    fecha: this.fecha,
                    fecha_requisicion: this.fecha_requisicion,
                    partidas: this.partidas,
                    id_area_compradora: this.id_area_compradora,
                    id_area_solicitante: this.id_area_solicitante,
                    folio_requisicion: this.folio_req,
                    observaciones: this.observaciones,
                    id_tipo: this.id_tipo
                })
                .then((data) => {
                    this.$router.push({name: 'solicitud-compra'});
                });
            },
        },
        watch: {
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_almacen_temporal = '';
                    this.destino_seleccionado.id_destino = value;
                    this.destino_seleccionado.tipo_destino = 1;
                    this.getConcepto();
                }
            },
            id_almacen_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.id_concepto_temporal = '';
                    this.destino_seleccionado.id_destino = value;
                    this.destino_seleccionado.tipo_destino = 2;
                    this.getAlmacen();
                }
            },
        }
    }
</script>

<style>

</style>
