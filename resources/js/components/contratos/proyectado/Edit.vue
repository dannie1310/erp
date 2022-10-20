<template>
    <span>
        <div class="card" v-if="!contrato">
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
            <form role="form" @submit.prevent="validate">
                <div class="card-body" v-if="contrato">
                    <encabezado-contrato-proyectado v-bind:contrato_proyectado="contrato"></encabezado-contrato-proyectado>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group error-content">
                                <label class="col-form-label">Fecha:</label>
                                <datepicker v-model="contrato.fecha_date"
                                            name = "fecha"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha')}"/>
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                            </div>
                        </div>
                        <br />
                        <div class="col-md-6"></div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12" style="height: 10px">
                                    <label><b>Fechas Límite</b></label>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="cotizacion">Cotización</label>
                                        <input  v-model="contrato.cumplimiento"
                                                type="date"
                                                name="cotizacion"
                                                id="cotizacion"
                                                class="form-control"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Cotización"
                                                :class="{'is-invalid': errors.has('cotizacion')}">
                                        <div class="invalid-feedback" v-show="errors.has('cotizacion')">{{ errors.first('cotizacion') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="contratacion">Contratación</label>
                                        <input  v-model="contrato.vencimiento"
                                                type="date"
                                                name="contratacion"
                                                id="contratacion"
                                                class="form-control"
                                                v-validate="{required: true, date_format: 'yyyy-MM-dd'}"
                                                data-vv-as="Contratación"
                                                :class="{'is-invalid': errors.has('contratacion')}">
                                        <div class="invalid-feedback" v-show="errors.has('contratacion')">{{ errors.first('contratacion') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row error-content">
                                <label for="referencia" class="col-sm-2 col-form-label">Referencia</label>
                                <div class="col-sm-12">
                                    <input
                                        v-model="contrato.referencia"
                                        type="text"
                                        name="referencia"
                                        data-vv-as="Referencia"
                                        v-validate="{required: true}"
                                        class="form-control"
                                        id="referencia"
                                        placeholder="Referencia"
                                        :class="{'is-invalid': errors.has('referencia')}">
                                    <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row" v-if="contrato.puede_editar_partidas">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-success" @click="agregarPartida('')"><i class="fa fa-plus"></i>Agregar Partida</button>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="contrato.puede_editar_partidas == false && reclasificar_destinos">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" @click="reclasificar">
                                    <i class="fa fa-random" aria-hidden="true"></i> Reclasificar Destino
                                </button>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div  class="col-12">
                            <div class="table-responsive">
                                <table v-if="contrato.puede_editar_partidas" class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th class="index_corto"></th>
                                        <th class="c120">Clave</th>
                                        <th >Descripción</th>
                                        <th class="c150">Unidad</th>
                                        <th class="c150">Cantidad</th>
                                        <th >Destinos</th>
                                        <th class="c100"></th>
                                        <th class="index_corto"></th>
                                    </tr>
                                    </thead>
                                    <tbody v-for="(partida, i) in contrato.contratos.data">
                                        <tr>
                                            <td class="icono">
                                                <button @click="agregarPartida(i)" type="button" class="btn btn-sm btn-outline-success" :disabled="cargando" title="Agregar">
                                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                    <i class="fa fa-plus" v-else></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                       :name="`clave[${i}]`"
                                                       data-vv-as="Clave"
                                                       v-model="partida.clave"
                                                       v-validate="{max:140}"
                                                       :class="{'is-invalid': errors.has(`clave[${i}]`)}"
                                                       :id="`clave[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`clave[${i}]`)">{{ errors.first(`clave[${i}]`) }}</div>
                                            </td>
                                            <td>
                                                 <input type="text" class="form-control"
                                                        v-model="partida.descripcion"
                                                        readonly="readonly"
                                                        @click="habilitar(i, $event)"
                                                        @focusout="deshabilitar(i, $event)"
                                                        :name="`descripcion[${i}]`"
                                                        data-vv-as="Descripción"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has(`descripcion[${i}]`)}"
                                                        :id="`descripcion_${i}`">
                                                <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                            </td>
                                            <td v-if="partida.es_hoja">
                                                <select
                                                    :disabled="!partida.es_hoja"
                                                    type="text"
                                                    :name="`unidad[${i}]`"
                                                    data-vv-as="Unidad"
                                                    v-validate="{required: partida.es_hoja}"
                                                    class="form-control"
                                                    :id="`unidad[${i}]`"
                                                    v-model="partida.unidad"
                                                    :class="{'is-invalid': errors.has(`unidad[${i}]`)}">
                                                    <option value>--Unidad--</option>
                                                    <option v-for="unidad in unidades" :value="unidad.unidad">{{ unidad.descripcion }}</option>
                                                </select>
                                                <div class="invalid-feedback" v-show="errors.has(`unidad[${i}]`)">{{ errors.first(`unidad[${i}]`) }}</div>
                                            </td>
                                            <td v-else><input type="text" disabled="true" class="form-control" readonly="readonly"></td>
                                            <td v-if="partida.es_hoja">
                                                <input type="number" class="form-control" :disabled="!partida.es_hoja"
                                                       :name="`cantidad[${i}]`"
                                                       data-vv-as="Cantidad"
                                                       step="any"
                                                       v-model="partida.cantidad_original"
                                                       v-validate="{required: partida.es_hoja, decimal:4}"
                                                       :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                       :id="`cantidad[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                            </td>
                                            <td v-else><input type="text" disabled="true" class="form-control" readonly="readonly"></td>
                                            <td v-if="!partida.es_hoja">
                                                <input type="text" disabled="true" class="form-control" readonly="readonly">
                                            </td>
                                            <td v-else-if="partida.cantidad_original == 0">
                                                <input type="text" class="form-control"
                                                       value=""
                                                       readonly="readonly"
                                                       :title="partida.destino"
                                                       :name="`destino_path[${i}]`"
                                                       data-vv-as="Destino"
                                                       v-model="partida.destino"
                                                       v-validate="{required: partida.es_hoja}"
                                                       :class="{'is-invalid': errors.has(`destino_path[${i}]`)}"
                                                       :id="`destino_path[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`destino_path[${i}]`)">{{ errors.first(`destino_path[${i}]`) }}</div>
                                            </td>
                                            <td v-else>
                                                 <input type="text" class="form-control"
                                                        readonly="readonly"
                                                        :title="partida.destino ? partida.destino.concepto.path : partida.destino"
                                                        :name="`destino_path[${i}]`"
                                                        data-vv-as="Destino"
                                                        v-model="partida.destino ? partida.destino.concepto.path_corta : partida.destino"
                                                        v-validate="{required: partida.es_hoja}"
                                                        :class="{'is-invalid': errors.has(`destino_path[${i}]`)}"
                                                        :id="`destino_path[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`destino_path[${i}]`)">{{ errors.first(`destino_path[${i}]`) }}</div>
                                            </td>
                                            <td class="icono">
                                                <small class="badge badge-secondary">
                                                    <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" v-if="partida.es_hoja"></i>
                                                </small>
                                                <i class="far fa-copy button" v-on:click="copiar_destino(partida)" v-if="partida.es_hoja"></i>
                                                <i class="fas fa-paste button" v-on:click="pegar_destino(i)" v-if="partida.es_hoja"></i>
                                            </td>
                                            <td class="icono">
                                                <button @click="eliminarPartida(i)" type="button" class="btn btn-sm btn-outline-danger pull-left" :disabled="!partida.es_hoja && partida.cantidad_hijos > 0" title="Eliminar">
                                                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                    <i class="fa fa-trash" v-else></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table v-else class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th class="index_corto">#</th>
                                            <th class="c120">Clave</th>
                                            <th >Descripción</th>
                                            <th class="c150">Unidad</th>
                                            <th class="cantidad_input">Cantidad</th>
                                            <th>Destinos</th>
                                            <th class="c100"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(concepto, i) in contrato.contratos.data">
                                        <tr>
                                            <td>{{i+1}}</td>
                                            <td :title="concepto.clave">{{concepto.clave}}</td>
                                            <td :title="concepto.descripcion">{{concepto.descripcion_guion}}</td>
                                            <td v-if="concepto.unidad == null"></td>
                                            <td v-else class="c150">{{concepto.unidad}}</td>
                                            <td v-if="concepto.unidad == null"></td>
                                            <td v-else class="cantidad_input">{{concepto.cantidad_original_format}}</td>
                                            <td v-if="concepto.unidad == null"></td>
                                            <td v-else-if="editar_destinos && (concepto.destino == undefined || concepto.id_destino)">
                                               <input type="text" class="form-control"
                                                       value=""
                                                       readonly="readonly"
                                                       :name="`destino_path[${i}]`"
                                                       data-vv-as="Destino"
                                                       v-model="concepto.destino"
                                                       v-validate="{required: concepto.es_hoja}"
                                                       :class="{'is-invalid': errors.has(`destino_path[${i}]`)}"
                                                       :id="`destino_path[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`destino_path[${i}]`)">{{ errors.first(`destino_path[${i}]`) }}</div>
                                            </td>
                                            <td v-else-if="concepto.destino|| editar_destinos">
                                                <input type="text" class="form-control"
                                                       value=""
                                                       readonly="readonly"
                                                       :title="concepto.destino.concepto ? concepto.destino.concepto.path : concepto.destino"
                                                       :name="`destino_path[${i}]`"
                                                       data-vv-as="Destino"
                                                       v-model="concepto.destino.concepto ? concepto.destino.concepto.path : concepto.destino"
                                                       v-validate="{required: concepto.es_hoja}"
                                                       :class="{'is-invalid': errors.has(`destino_path[${i}]`)}"
                                                       :id="`destino_path[${i}]`">
                                                <div class="invalid-feedback" v-show="errors.has(`destino_path[${i}]`)">{{ errors.first(`destino_path[${i}]`) }}</div>
                                            </td>
                                            <td v-else :title="concepto.destino.concepto.path" style="text-decoration: underline">
                                                {{concepto.destino.concepto.path_corta}}
                                            </td>
                                            <td class="icono" v-if="concepto.es_hoja">
                                                <small class="badge badge-secondary">
                                                    <i class="fa fa-sign-in button" aria-hidden="true" v-on:click="modalDestino(i)" v-if="concepto.destino == undefined || concepto.id_destino || editar_destinos" title="Seleccionar Destino"></i>
                                                </small>
                                                <i class="far fa-copy button" v-on:click="copiar_destino(concepto)" v-if="concepto.destino == undefined || concepto.id_destino || editar_destinos" title="Copiar"></i>
                                                <i class="fas fa-paste button" v-on:click="pegar_destino(i)" v-if="concepto.destino == undefined || concepto.id_destino || editar_destinos" title="Pegar"></i>
                                            </td>
                                            <td v-else></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
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
                                        <label for="id_concepto" class="col-md-2 col-form-label">Conceptos:</label>
                                        <div class="col-md-10">
                                            <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="destino_temp"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="true"
                                            ></concepto-select>
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalDestino" :disabled="cargando">
                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                <i class="fa fa-close" v-else ></i> Cerrar</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>
<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import  ConceptoSelect from "../../cadeco/concepto/Select";
    import EncabezadoContratoProyectado from "./Encabezado";
export default {
    name: "contrato-proyectado-editar",
    components: {EncabezadoContratoProyectado, Datepicker, es, ConceptoSelect},
    props: ['id'],
    data() {
        return {
            cargando: false,
            es:es,
            contrato : null,
            fecha : '',
            vencimiento : '',
            cumplimiento : '',
            referencia : '',
            unidades : [],
            destino_temp : '',
            partida_copia:{
                destino:'',
                id_destino:''
            },
            editar_destinos: false,
            reclasificar_destinos: false,
        }
    },
    mounted() {
        this.find();
        this.getUnidades();
    },
    methods: {
        salir() {
            this.$router.push({name: 'proyectado'});
        },
        save() {
            this.contrato.editar_destinos = this.editar_destinos;
            return this.$store.dispatch('contratos/contrato-proyectado/update', {
                id: this.id,
                data: this.contrato,
                })
                .then(() => {
                    this.salir()
                })
        },
        formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
        },
        getUnidades() {
            return this.$store.dispatch('cadeco/unidad/index', {
                params: {sort: 'unidad',  order: 'asc'}
            })
                .then(data => {
                    this.unidades= data.data;
                })
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/find', {
                id: this.id,
                params:{ include: [ 'contratos.destino' ]}
            }).then(data => {
                this.contrato = data;
                for (var i = 0; i < data.contratos.data.length; i++)
                {
                    var len = this.contrato.contratos.data[i].descripcion.length + (+this.contrato.contratos.data[i].nivel_num * 3);
                    this.contrato.contratos.data[i].descripcion = this.contrato.contratos.data[i].descripcion.padStart(len, "_")
                }
                this.fecha = data.fecha_date;
                this.referencia = data.referencia;
                this.cumplimiento = data.cumplimiento;
                this.vencimiento = data.vencimiento;
                if(this.$root.can('editar_destinos_contrato_proyectado') != undefined)
                {
                    this.editar_destinos = true;
                }
                if(this.$root.can('reclasificar_destinos_contrato_proyectado') != undefined)
                {
                    this.reclasificar_destinos = true;
                }
            }).finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.save()
                }
            });
        },
        agregarPartida(index){
            if(index === ''){
                this.contrato.contratos.data.push({
                    clave:'',
                    descripcion:'',
                    unidad:'',
                    cantidad:'',
                    destino:'',
                    destino_path:'',
                    nivel_num: 1,
                    es_hoja:true,
                    cantidad_hijos:0,
                });
            }else{
                let temp_index = index + 1;
                while(temp_index in this.contrato.contratos.data && this.contrato.contratos.data[temp_index].nivel_num >= +this.contrato.contratos.data[index].nivel_num + 1){
                    temp_index= temp_index + 1;
                }
                this.contrato.contratos['data'].splice(temp_index, 0, {
                    clave:'',
                    descripcion:'',
                    unidad:'',
                    cantidad:'',
                    destino:'',
                    destino_path:'',
                    nivel_num: this.contrato.contratos.data[index].nivel_num + 1,
                    es_hoja: true,
                    cantidad_hijos:0
                });
                this.contrato.contratos.data[index].es_hoja = false;
                this.contrato.contratos.data[index].es_rama = true;
                this.contrato.contratos.data[index].unidad = '';
                this.contrato.contratos.data[index].cantidad = '';
                this.contrato.contratos.data[index].destino = '';
                this.contrato.contratos.data[index].cantidad_hijos = this.contrato.contratos.data[index].cantidad_hijos + 1;
            }
        },
        cerrarModalDestino(){
            this.destino_temp = '';
            $(this.$refs.modal_destino).modal('hide');
            this.$validator.reset();
        },
        descripcionFormat(i){
            var len = this.contrato.contratos.data[i].descripcion.length + (+this.contrato.contratos.data[i].nivel_num * 3);
            return this.contrato.contratos.data[i].descripcion.padStart(len, "_")
        },
        descripcionSinFormat(i){
            var len = (this.contrato.contratos.data[i].nivel_num * 3);
            let lineas = '';
            lineas = lineas.padStart(len, "_");
            return this.contrato.contratos.data[i].descripcion.replace(lineas, '');
        },
        habilitar : function(i, event){
            let nuevo_valor = this.descripcionSinFormat(i);
            this.contrato.contratos.data[i].descripcion = nuevo_valor;
            $("#" + event.target.id).removeAttr("readonly");
        },
        deshabilitar : function(i,event){
            let isReadOnly = $("#" + event.target.id).attr("readonly");
            if(isReadOnly !== "readonly"){
                let nuevo_valor = this.descripcionFormat(i);
                this.contrato.contratos.data[i].descripcion = nuevo_valor;
                $("#" + event.target.id).attr("readonly",true);
            }
        },
        editDestino(index){
            this.edit_destino_index = index;
            this.destino_temp = this.contrato.contratos.data[index].destino;
            $(this.$refs.modalDestino).appendTo('body')
            $(this.$refs.modalDestino).modal('show')
        },
        eliminarPartida(index){
            if(this.contrato.contratos.data[index].nivel_num === 1){
                this.contrato.contratos.data.splice(index, 1);
            }else{
                let temp_index = index - 1;
                while(temp_index in this.contrato.contratos.data && this.contrato.contratos.data[temp_index].nivel_num == +this.contrato.contratos.data[index].nivel_num){
                    temp_index= temp_index - 1;
                }
                this.contrato.contratos.data[temp_index].cantidad_hijos = this.contrato.contratos.data[temp_index].cantidad_hijos - 1;
                this.contrato.contratos.data.splice(index, 1);
                if(this.contrato.contratos.data[temp_index].cantidad_hijos == 0){
                    this.contrato.contratos.data[temp_index].es_hoja = true;
                    this.contrato.contratos.data[temp_index].destino = '';
                }
            }
        },
        modalDestino(index) {
            this.partida_index = index;
            this.$validator.reset();
            $(this.$refs.modal_destino).modal('show');
        },
        copiar_destino(partida){
            if(partida.hasOwnProperty('id_destino')) {
                this.partida_copia.destino = partida.destino;
                this.partida_copia.id_destino = partida.id_destino;
            }else{
                this.partida_copia.destino = partida.destino ? partida.destino.concepto.path_corta : '';
                this.partida_copia.id_destino = partida.destino ? partida.destino.id_concepto : '';
            }
        },
        pegar_destino(index){
            if(this.contrato.contratos.data[index].hasOwnProperty('id_destino')) {
                this.contrato.contratos.data[index].destino = this.partida_copia.destino;
                this.contrato.contratos.data[index].id_destino = this.partida_copia.id_destino;
            }else{
                this.contrato.contratos.data[index].destino = this.partida_copia.destino;
                this.contrato.contratos.data[index].id_destino = this.partida_copia.id_destino;
            }
            this.$forceUpdate();
        },
        getConcepto(id_concepto) {
            this.cargando = true;
            return this.$store.dispatch('cadeco/concepto/find', {
                id: id_concepto
            })
            .then(data => {
                let path = data.path.split('->');
                this.contrato.contratos.data[this.partida_index].id_destino = data.id;
                this.contrato.contratos.data[this.partida_index].destino = path[path.length - 2] + ' -> ' + data.path_corta;
            })
            .finally(()=> {
                this.partida_index = '';
                this.cargando = false;
                $(this.$refs.modal_destino).modal('hide');
            })
        },
        reclasificar() {
            return this.$store.dispatch('contratos/contrato-proyectado/reclasificarDestino', {
                id: this.id,
                data: this.contrato,
            }).then(() => {
                this.salir()
            })
        },
    },
    watch: {
        destino_temp(value) {
            if (value !== '' && value !== null && value !== undefined) {
                this.getConcepto(value);
            }
        },
    }
}
</script>
<style>
    .icons
    {
        text-align: center;
    }
</style>
