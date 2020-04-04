<template>
    <span>
        <nav>
            <div class="row"><div class="col-12">
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
                                        <label for="id_area_compradora">Departamento Responsable</label>
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
                                        <label for="id_tipo">Tipo</label>
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="id_area_solicitante">Área Solicitante</label>
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
                                        <label>Fecha Requisición Origen:</label>
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
                                        <label>Folio Requisición Origen</label>
                                        <input
                                            type="text"
                                            data-vv-as="Folio Requisición"
                                            class="form-control"
                                            placeholder="Folio Requisición"
                                            v-model="folio_req"/>
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
                                                <th style="width:130px;">No. de Parte</th>
                                                <th>Descripción</th>
                                                <th class="icono"></th>
                                                <th class="cantidad_input">Cantidad</th>
                                                <th class="unidad">Unidad</th>
                                                <th style="width:140px;">Fecha Entrega</th>
                                                <th class="icono"></th>
                                                <th>Destino</th>
                                                <th>Observaciones</th>
                                                <th class="icono">
                                                    <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(partida, i) in partidas">
                                                <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                <td v-if="partida.i === 0 && partida.material === ''">
                                                </td>
                                                <td v-else-if="partida.i === 1">
                                                    <input
                                                        type="text"
                                                        data-vv-as="Número Parte"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        :name="`numero_parte[${i}]`"
                                                        placeholder="Número Parte"
                                                        v-model="partida.numero_parte"
                                                        :class="{'is-invalid': errors.has(`numero_parte[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`numero_parte[${i}]`)">{{ errors.first(`numero_parte[${i}]`) }}</div>
                                                </td>
                                                <td v-else>{{partida.material.numero_parte}}</td>
                                                <td v-if="partida.i === 0 && partida.material === ''">
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
                                                <td v-else-if="partida.i === 1">
                                                    <input
                                                        type="text"
                                                        data-vv-as="Descripción"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        :name="`descripcion[${i}]`"
                                                        placeholder="Descripción"
                                                        v-model="partida.descripcion"
                                                        :class="{'is-invalid': errors.has(`descripcion[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                                </td>
                                                <td v-else>{{partida.material.descripcion}}</td>
                                                <td v-if="partida.i === 0">
                                                    <button  type="button" class="btn btn-outline-primary btn-sm" @click="manual(i)" title="Ingresar material manualmente"><i class="fa fa-hand-paper-o" /></button>
                                                </td>
                                                <td v-else-if="partida.i === 1">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" @click="busqueda(i)" title="Buscar material"><i class="fa fa-refresh" /></button>
                                                </td>
                                                <td style="width: 30px;" v-else></td>
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
                                                <td style="width:120px;" v-if="partida.i === 1">
                                                    <select
                                                        type="text"
                                                        :name="`unidad[${i}]`"
                                                        data-vv-as="Unidad"
                                                        v-validate="{required: true}"
                                                        class="form-control"
                                                        id="unidad"
                                                        v-model="partida.unidad"
                                                        :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                        <option value>--Unidad--</option>
                                                        <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                    </select>
                                                    <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                                </td>
                                                <td style="width:120px;" v-else-if="partida.unidad">{{partida.unidad}}</td>
                                                <td style="width:120px;" v-else>{{partida.material.unidad}}</td>
                                                <td class="fecha">
                                                    <datepicker v-model="partida.fecha"
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
                                                <td style="text-align:center;"><small class="badge badge-secondary">
                                                    <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)"></i>
                                                </small></td>
                                                <td style="width:140px;" v-if="partida.clave_concepto"><u>{{partida.clave_concepto.descripcion}}</u></td>
                                                <td style="width:140px; text-align:center;" v-else-if="partida.destino">{{partida.destino.descripcion}}</td>
                                                <td style="width:140px; text-align:center;" v-else></td>
                                                <td style="width:200px;">
                                                    <textarea class="form-control"
                                                              :name="`observaciones[${i}]`"
                                                              data-vv-as="Observaciones"
                                                              v-validate="{required: true}"
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
                            <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Registrar</button>
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
                                        <label class="col-sm-2 col-form-label">Conceptos:</label>
                                        <div class="col-sm-10">
                                            <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="id_concepto_temporal"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="false"
                                            ></concepto-select>
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
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
        name: "solicitud-compra-create",
        components: {Datepicker, ModelListSelect, es,ConceptoSelect},
        data(){
            return{
                cargando: false,
                es:es,
                fechasDeshabilitadas:{},
                fechasDeshabilitadasHasta:{},
                fecha : '',
                fecha_requisicion : '',
                fecha_hoy : '',
                id_material: '',
                areas_compradoras : [],
                areas_solicitantes : [],
                tipos : [],
                id_area_compradora : '',
                id_concepto_temporal : '',
                id_tipo : '',
                materiales : [],
                id_area_solicitante : '',
                concepto : '',
                observaciones : '',
                unidades : [],
                folio_req : '',
                destino_seleccionado: {
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
                        concepto_temporal : ""
                    }
                ],
            }
        },
        mounted() {
            this.$validator.reset()
            this.getAreasCompradoras();
            this.getAreasSolicitantes();
            this.getTipos();
            this.getUnidades();
            this.getMateriales();
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
                this.unidades = [];
                this.partidas = [{
                    i : 0,
                    material : "",
                    unidad : "",
                    numero_parte : "",
                    descripcion : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                    concepto_temporal : ""
                }];
            },
            changeSelect(item){
                var busqueda = this.materiales.find(x=>x.id === item.id_material);
                if(busqueda != undefined)
                {
                    item.material = busqueda;
                }
            },
            modalDestino(i) {
                this.partidas[i].clave_concepto = '';
                this.destino_seleccionado.destino = '';
                this.index_temporal = i;
                $(this.$refs.modal_destino).modal('show');
            },
            cerrarModalDestino(){
                this.id_concepto_temporal = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
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
            getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                    .then(data => {
                        this.unidades= data.data;
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
            seleccionarDestino() {
                this.partidas[this.index_temporal].destino = this.destino_seleccionado.destino;
                this.partidas[this.index_temporal].clave_concepto = this.destino_seleccionado.destino;
                this.index_temporal = '';
                this.destino_seleccionado = {
                    tipo_destino : '',
                    destino : '',
                    id_destino : ''
                };
                this.id_concepto_temporal = '';

                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
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
                this.partidas.splice(this.partidas.length + 1, 0, {
                    i : 0,
                    material : "",
                    descripcion : "",
                    unidad : "",
                    numero_parte : "",
                    cantidad : "",
                    fecha : "",
                    observaciones : "",
                    concepto_temporal : ""
                });
                this.index = this.index+1;
            },
            salir(){
                this.$router.push({name: 'solicitud-compra'});
            },
            destroy(index){
                this.partidas.splice(index, 1);
            },
            manual(index){
                this.partidas[index].material = ""
                this.partidas[index].id_material = ""
                this.partidas[index].i = 1;
            },
            busqueda(index){
                this.partidas[index].unidad = ""
                this.partidas[index].descripcion = ""
                this.partidas[index].numero_parte = ""
                this.partidas[index].material = ""
                this.partidas[index].id_material = ""
                this.partidas[index].i = 0;
            },
            getMateriales() {
                this.materiales = [];
                this.cargando = true;
                return this.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: 'materialesParaCompras',
                        sort: 'descripcion', order: 'desc'
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
                        this.store()
                    }
                });
            },
            store() {
                this.t = 0;
                this.m = 0;
                while(this.t < this.partidas.length){
                    if(typeof this.partidas[this.t].clave_concepto === 'undefined' || this.partidas[this.t].clave_concepto === '')
                    {
                        this.m ++;
                        swal('¡Error!', 'Ingrese un destino válido en partida '+(this.t + 1) +'.', 'error');
                    }
                    this.t ++;
                }if(this.m == 0)
                {
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
                            this.$router.push({name: 'solicitud-compra-index'});
                        });

                }
            },
        },
        watch: {
            id_concepto_temporal(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.destino_seleccionado.id_destino = value;
                    this.getConcepto();
                }
            },
        }
    }
</script>

<style>

</style>
