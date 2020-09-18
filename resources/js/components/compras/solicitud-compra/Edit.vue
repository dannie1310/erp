<template>
    <span>
        <nav>
            <div class="row" v-if="!cargando">
                <div class="col-12">
                    <div class="invoice p-3 mb-3" v-if="solicitud">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group error-content">
                                            <label class="col-form-label">Fecha:</label>
                                            <datepicker v-model = "solicitud.fecha"
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
                                <div class="row justify-content-between" v-if="solicitud.complemento">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_area_compradora">Departamento Responsable</label>
                                            <select class="form-control"
                                                    name="id_area_compradora"
                                                    data-vv-as="Departamento Responsable"
                                                    v-model="solicitud.complemento.id_area_compradora"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_compradora')">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="area in areas_compradoras" :value="area.id" >{{ area.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_compradora')">{{ errors.first('id_area_compradora') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_tipo">Tipo</label>
                                            <select class="form-control"
                                                    data-vv-as="Tipo"
                                                    name="id_tipo"
                                                    :error="errors.has('id_tipo')"
                                                    v-validate="{required: true}"
                                                    v-model="solicitud.complemento.id_tipo">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(tipo, index) in tipos" :value="tipo.id" >{{ tipo.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_tipo')">{{ errors.first('id_tipo') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"  v-if="configuracion && configuracion.configuracion_area_solicitante == 1">
                                        <div class="form-group">
                                            <label for="id_area_solicitante">Área Solicitante</label>
                                            <select class="form-control"
                                                    data-vv-as="Área Solicitante"
                                                    name="id_area_solicitante"
                                                    v-model="solicitud.complemento.id_area_solicitante"
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
                                            <label>Fecha Requisición Origen:</label>
                                            <datepicker v-model = "solicitud.complemento.fecha_requisicion_origen"
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
                                            <label>Folio Requisición Origen</label>
                                            <input
                                                type="number"
                                                data-vv-as="Folio Requisición"
                                                class="form-control"
                                                placeholder="Folio Requisición"
                                                v-model="solicitud.complemento.requisicion_origen"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-between" v-else>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_area_compradora">Departamento Responsable</label>
                                            <select class="form-control"
                                                    name="id_area_compradora"
                                                    data-vv-as="Departamento Responsable"
                                                    v-model="solicitud.id_area_compradora"
                                                    v-validate="{required: true}"
                                                    :error="errors.has('id_area_compradora')">
                                                <option value>-- Seleccionar--</option>
                                                <option v-for="area in areas_compradoras" :value="area.id" >{{ area.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_area_compradora')">{{ errors.first('id_area_compradora') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="id_tipo">Tipo</label>
                                            <select class="form-control"
                                                    data-vv-as="Tipo"
                                                    name="id_tipo"
                                                    :error="errors.has('id_tipo')"
                                                    v-validate="{required: true}"
                                                    v-model="solicitud.id_tipo">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(tipo, index) in tipos" :value="tipo.id" >{{ tipo.descripcion}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_tipo')">{{ errors.first('id_tipo') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" v-if="configuracion && configuracion.configuracion_area_solicitante == 1">
                                        <div class="form-group">
                                            <label for="id_area_solicitante">Área Solicitante</label>
                                            <select class="form-control"
                                                    data-vv-as="Área Solicitante"
                                                    name="id_area_solicitante"
                                                    v-model="solicitud.id_area_solicitante"
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
                                            <label>Fecha Requisición Origen:</label>
                                            <datepicker v-model = "solicitud.fecha_requisicion_origen"
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
                                            <label>Folio Requisición Origen</label>
                                            <input
                                                type="number"
                                                data-vv-as="Folio Requisición"
                                                class="form-control"
                                                placeholder="Folio Requisición"
                                                v-model="solicitud.requisicion_origen"/>
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
                                        <div class="form-group row error-content" v-if="solicitud.complemento">
                                                <textarea
                                                    name="concepto"
                                                    class="form-control"
                                                    v-model="solicitud.complemento.concepto"
                                                    v-validate="{required: true}"
                                                    data-vv-as="Concepto"
                                                    :class="{'is-invalid': errors.has('concepto')}"
                                                ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                                        </div>
                                        <div class="form-group row error-content" v-else>
                                                <textarea
                                                    name="concepto"
                                                    class="form-control"
                                                    v-model="solicitud.concepto"
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
                                                    <th style="width:130px;">No. de Parte</th>
                                                    <th>Descripción</th>
                                                    <th class="cantidad_input">Cantidad</th>
                                                    <th class="unidad">Unidad</th>
                                                    <th style="width:140px;">Fecha Entrega</th>
                                                    <th>Destino</th>
                                                    <th class="icono"></th>
                                                    <th>Observaciones</th>
                                                    <th class="icono">
                                                        <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="solicitud.partidas">
                                                <tr v-for="(partida, i) in solicitud.partidas.data">
                                                   <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                    <td v-if="partida.material === ''" />
                                                    <td v-else>{{partida.material.numero_parte}}</td>
                                                    <td v-if="partida.material">{{partida.material.descripcion}}</td>
                                                    <td v-else>
                                                        <model-list-select
                                                            :name="`material[${i}]`"
                                                            v-validate="{required: true}"
                                                            v-model="partida.id_material"
                                                            :onchange="changeSelect(partida)"
                                                            option-value="id"
                                                            :custom-text="idAndNumeroParteAndDescripcion"
                                                            :list="materiales"
                                                            :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                                            :isError="errors.has(`material[${i}]`)">
                                                        </model-list-select>
                                                        <div class="invalid-feedback" v-show="errors.has('id_material')">{{ errors.first('id_material') }}</div>
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                               min="0.01"
                                                               step=".01"
                                                               class="form-control"
                                                               :name="`cantidad[${i}]`"
                                                               data-vv-as="Cantidad"
                                                               v-validate="{required: true}"
                                                               :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                               v-model="partida.cantidad"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                    </td>
                                                    <td style="width:120px;" v-if="partida.material">{{partida.material.unidad}}</td>
                                                    <td style="width:120px;" v-else></td>
                                                    <td class="fecha" v-if="partida.entrega">
                                                        <datepicker v-model="partida.entrega.fecha"
                                                                    :name="`fecha[${i}]`"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :disabled-dates="fechasDeshabilitadasHasta"
                                                                    :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                        ></datepicker>
                                                        <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                    </td>
                                                    <td class="fecha" v-else>
                                                        <datepicker v-model="partida.fecha_entrega"
                                                                    :name="`fecha[${i}]`"
                                                                    :format = "formatoFecha"
                                                                    :language = "es"
                                                                    :bootstrap-styling = "true"
                                                                    class = "form-control"
                                                                    v-validate="{required: true}"
                                                                    :disabled-dates="fechasDeshabilitadasHasta"
                                                                    :class="{'is-invalid': errors.has(`fecha[${i}]`)}"
                                                        ></datepicker>
                                                        <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                    </td>
                                                    <td v-if="partida.destino == undefined && (partida.entrega != undefined && (partida.entrega.concepto != undefined || partida.entrega.almacen != undefined))">
                                                        <small class="badge badge-success" v-if="partida.entrega.concepto != undefined && partida.entrega.concepto != null">
                                                            <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <small class="badge badge-success" v-if="partida.entrega.almacen != undefined && partida.entrega.almacen != null">
                                                            <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <span v-if="partida.entrega.concepto != undefined && partida.entrega.concepto != null" style="text-decoration: underline" :title="partida.entrega.concepto.path">{{partida.entrega.concepto.descripcion}}</span>
                                                        <span v-if="partida.entrega.almacen != undefined && partida.entrega.almacen != null">{{partida.entrega.almacen.descripcion}}</span>
                                                    </td>
                                                    <td v-else-if="partida.destino">
                                                        <small class="badge badge-success" v-if="partida.destino.tipo_destino === 1" >
                                                            <i class="fa fa-stream button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <small class="badge badge-success" v-if="partida.destino.tipo_destino === 2">
                                                            <i class="fa fa-boxes button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                        <span v-if="partida.destino.tipo_destino === 1 && partida.destino.destino" style="text-decoration: underline" :title="partida.destino.destino.path">{{partida.destino.destino.descripcion}}</span>
                                                        <span v-if="partida.destino.tipo_destino === 2">{{partida.destino.destino.descripcion}}</span>
                                                    </td>
                                                    <td v-else>
                                                        <small class="badge badge-secondary">
                                                            <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" ></i>
                                                        </small>
                                                    </td>
                                                    <td class="icono">
                                                        <i class="far fa-copy button" v-on:click="copiar_destino(partida)" title="Copiar" ></i>
                                                        <i class="fas fa-paste button" v-on:click="pegar_destino(partida)" title="Pegar"></i>
                                                    </td>
                                                    <td style="width:150px;" v-if="partida.complemento">
                                                        <textarea class="form-control"
                                                                  :name="`observaciones[${i}]`"
                                                                  data-vv-as="Observaciones"
                                                                  v-validate="{}"
                                                                  :class="{'is-invalid': errors.has(`observaciones[${i}]`)}"
                                                                  v-model="partida.complemento.observaciones"/>
                                                        <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                    </td>
                                                    <td style="width:150px;" v-else>
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
                                                    v-model="solicitud.observaciones"
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
                               <button type="submit" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0">
                                   <i class="fa fa-save"></i>
                                   Guardar
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
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    export default {
        name: "solicitud-compra-edit",
        components: {Datepicker, ModelListSelect, es,ConceptoSelect},
        props: ['id'],
        data(){
            return{
                cargando: false,
                es:es,
                configuracion:'',
                solicitud: [],
                fechasDeshabilitadas:{},
                fechasDeshabilitadasHasta:{},
                fecha : '',
                fecha_hoy : '',
                areas_compradoras : [],
                areas_solicitantes : [],
                tipos : [],
                index_temporal : '',
                id_almacen_temporal : '',
                id_concepto_temporal : '',
                materiales : [],
                almacenes : [],
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
            }
        },
        mounted() {
            this.$validator.reset();
            this.cargando = true;
            this.find()
            this.getAreasCompradoras();
            this.getConfiguracion();
            this.getTipos();
            this.getMateriales();
            this.getAlmacenes();
        },
        methods : {
            init() {
                this.fecha = new Date();
                this.areas_compradoras = '';
                this.areas_solicitantes = [];
                this.tipos = [];
            },
            find() {
                return this.$store.dispatch('compras/solicitud-compra/find', {
                    id: this.id,
                    params: { include : ['partidas.complemento', 'complemento', 'partidas.entrega']}
                }).then(data => {
                    this.solicitud = data;
                })
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
                var busqueda = this.materiales.find(x=>x.id === item.id_material);
                if(busqueda != undefined)
                {
                    item.material = busqueda;
                }
            },
            idAndNumeroParteAndDescripcion (item) {
                return `[${item.id}] - [${item.numero_parte}] -  ${item.descripcion}`
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreasCompradoras() {
                this.fecha_hoy = new Date();
                this.fecha_requisicion = new Date();
                this.fecha = new Date();
                this.fechasDeshabilitadas.from= new Date();
                this.fechasDeshabilitadasHasta.to= new Date();
                return this.$store.dispatch('configuracion/area-compradora/index', {
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
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
                        this.cargando = false;
                    })
            },
            modalDestino(i) {
                this.index_temporal = i;

                if(this.solicitud.partidas.data[this.index_temporal].entrega != undefined)
                {
                    this.destino_seleccionado.destino =  this.solicitud.partidas.data[this.index_temporal].entrega.almacen == undefined ? this.solicitud.partidas.data[this.index_temporal].entrega.concepto : this.solicitud.partidas.data[this.index_temporal].entrega.almacen;
                    this.destino_seleccionado.tipo_destino =   this.solicitud.partidas.data[this.index_temporal].entrega.concepto == undefined ? 1 : 2;
                    this.solicitud.partidas.data[this.index_temporal].entrega.concepto = null;
                    this.solicitud.partidas.data[this.index_temporal].entrega.almacen = null;
                    this.solicitud.partidas.data[this.index_temporal].destino = this.destino_seleccionado;
                }else{
                    if(this.solicitud.partidas.data[this.index_temporal].destino == '')
                    {
                        this.destino_seleccionado.tipo_destino =  '';
                        this.destino_seleccionado.destino = '';
                        this.destino_seleccionado.id_destino = '';
                    }
                    else {
                        this.destino_seleccionado = this.solicitud.partidas.data[this.index_temporal].destino;
                    }
                }

                if(this.almacenes.length == 0) {
                    this.getAlmacenes();
                }
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            seleccionarDestino() {
                this.solicitud.partidas.data[this.index_temporal].destino = this.destino_seleccionado;
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
                if(partida.destino != undefined)
                {
                    this.destino_copiado = partida.destino
                }
                if(partida.entrega != undefined)
                {
                    if(partida.entrega.almacen != undefined && partida.entrega.almacen != null)
                    {
                        this.destino_copiado.tipo_destino = 2
                        this.destino_copiado.destino = partida.entrega.almacen
                        this.destino_copiado.id_destino = partida.entrega.almacen.id
                    }
                    if(partida.entrega.concepto != undefined && partida.entrega.concepto != null)
                    {
                        this.destino_copiado.tipo_destino = 1
                        this.destino_copiado.destino = partida.entrega.concepto
                        this.destino_copiado.id_destino = partida.entrega.concepto.id
                    }
                }
            },
            pegar_destino(partida){
                if(partida.entrega != undefined)
                {
                    if(partida.entrega.almacen != undefined && partida.entrega.almacen != null)
                    {
                        partida.entrega.almacen = null
                    }
                    if(partida.entrega.concepto != undefined && partida.entrega.concepto != null)
                    {
                        partida.entrega.concepto = null
                    }
                }
                partida.destino = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                partida.destino.id_destino = this.destino_copiado.destino.id;
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
                    }).finally(() => {
                        this.cargando = false;
                    })
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
                    params: {scope: 'asignadas', sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        this.areas_solicitantes = data;
                        this.disabled = false;
                    })
            },
            addPartidas(){
                this.solicitud.partidas.data.splice(this.solicitud.partidas.data.length + 1, 0, {
                    material : "",
                    descripcion : "",
                    unidad : "",
                    numero_parte : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                    destino :  ""
                });
                this.index = this.index+1;
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            destroy(index){
                this.solicitud.partidas.data.splice(index, 1);
            },
            getMateriales() {
                this.materiales = [];
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: 'materialesParaCompras',
                        sort: 'descripcion', order: 'desc'
                    }
                })
                    .then(data => {
                        this.materiales = data.data;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        var t = 0;
                        var m = 0;
                        while(t < this.solicitud.partidas.data.length) {
                            if (typeof this.solicitud.partidas.data[t].entrega === 'undefined' && (this.solicitud.partidas.data[t].destino === '' || typeof this.solicitud.partidas.data[t].destino === 'undefined'))
                            {
                                this.m++;
                                swal('¡Error!', 'Ingrese un destino válido en partida ' + (t + 1) + '.', 'error');
                            }
                            t ++;
                        }if(m == 0) {
                            this.update()
                        }
                    }
                });
            },
            update() {
                return this.$store.dispatch('compras/solicitud-compra/update',
                    {
                        id: this.id,
                        data: this.solicitud
                })
                    .then((data) => {
                        this.$router.push({name: 'solicitud-compra'});
                    });
            }
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
