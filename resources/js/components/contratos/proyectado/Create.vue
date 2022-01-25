<template>
    <span>
        <div class="row">
            <div class="col-12">
                <form role="form" @submit.prevent="validate">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 ">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label for="fecha">Fecha</label>
                                            <datepicker v-model = "fecha"
                                                        id = "fecha"
                                                        name = "fecha"
                                                        data-vv-as="Fecha"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                                        class = "form-control">

                                            </datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <div class="form-group">
                                            <label for="fecha_cotizacion">Fecha de Cotización</label>
                                            <datepicker v-model = "fecha_cotizacion"
                                                        name = "fecha_cotizacion"
                                                        id="fecha_cotizacion"
                                                        data-vv-as="Fecha Cotización"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha_cotizacion')}"
                                                        class = "form-control">
                                            </datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha_cotizacion')">{{ errors.first('fecha_cotizacion') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group error-content">
                                         <div class="form-group">
                                             <label for="fecha_contrato">Fecha de Contratación</label>
                                             <datepicker v-model = "fecha_contrato"
                                                         name = "fecha_contrato"
                                                         id = "fecha_contrato"
                                                         data-vv-as="Fecha Contratación"
                                                         :language = "es"
                                                         :format = "formatoFecha"
                                                         :bootstrap-styling = "true"
                                                         v-validate="{required: true}"
                                                         :class="{'is-invalid': errors.has('fecha_contrato')}"
                                                         :disabled-dates="fechasDeshabilitadas"
                                                         class = "form-control">
                                             </datepicker>
                                             <div class="invalid-feedback" v-show="errors.has('fecha_contrato')">{{ errors.first('fecha_contrato') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia</label>
                                        <input type="text" class="form-control"
                                               name="referencia"
                                               data-vv-as="Referencia"
                                               v-model="referencia"
                                               v-validate="{required: true, max:64}"
                                               :class="{'is-invalid': errors.has('referencia')}"
                                               id="referencia"
                                               placeholder="Referencia">
                                        <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                     <div class="form-group error-content" v-if="areas_subcontratantes.length > 1">
                                        <label for="id_area">Área Subcontratante</label>
                                        <select
                                            type="text"
                                            name="id_area"
                                            data-vv-as="Área Subcontratante"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="id_area"
                                            v-model="id_area"
                                            :class="{'is-invalid': errors.has('id_area')}"
                                        >
                                        <option  value selected>--- Seleccione Área Subcontratante ---</option>
                                        <option v-for="area in areas_subcontratantes" :value="area.id">{{ `${area.descripcion} ` }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('id_area')">{{ errors.first('id_area') }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-success" @click="agregarPartida('')"><i class="fa fa-plus"></i>Agregar Partida</button>
                                        <button type="button" class="btn btn-success" @click="modalCarga()"><i class="fa fa-file-excel-o"></i>Cargar Layout</button>
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
                                            <tbody>
                                            <tr v-for="(partida, i) in partidas">
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
                                                            v-validate="{required: partida.descripcion ===''}"
                                                            :class="{'is-invalid': errors.has(`descripcion[${i}]`) || partida.error ==1 || partida.descripcion_sin_formato.length > 255}"
                                                            :id="`descripcion_${i}`">
                                                    <div class="invalid-feedback" v-show="errors.has(`descripcion[${i}]`)">{{ errors.first(`descripcion[${i}]`) }}</div>
                                                    <div class="error-label" v-show="partida.descripcion_sin_formato.length > 255">La longitud del campo Descripción no debe ser mayor a 255 caracteres.</div>
                                                </td>
                                                <td>
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
                                                <td>
                                                    <input type="number" class="form-control" :disabled="!partida.es_hoja"
                                                           :name="`cantidad[${i}]`"
                                                           data-vv-as="Cantidad"
                                                           step="any"
                                                           v-model="partida.cantidad"
                                                           v-validate="{required: partida.es_hoja, decimal:4}"
                                                           :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                           :id="`cantidad[${i}]`">
                                                    <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           readonly="readonly"
                                                           :title="partida.destino_path"
                                                           :name="`destino_path[${i}]`"
                                                           data-vv-as="Destino"
                                                           v-model="partida.destino_path"
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
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>Regresar</button>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>Guardar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
                                        <label for="id_concepto" class="col-sm-2 col-form-label">Conceptos:</label>
                                        <div class="col-sm-10">
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


        <!-- <div class="modal fade" ref="modal_carga" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-carga"> <i class="fa fa-file-excel-o"></i> Seleccionar Archivo de Layout</h5>
                        <button type="button" class="close" v-on:click="cerrarModalCarga" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row justify-content-between">
                                        <div class="col-md-12">
                                            <div class="col-lg-12">
                                                <input type="file" class="form-control" id="carga_layout"
                                                    @change="onFileChange"
                                                    row="3"
                                                    v-validate="{ ext: ['xlsx']}"
                                                    name="carga_layout"
                                                    data-vv-as="Layout"
                                                    ref="carga_layout"
                                                    :class="{'is-invalid': errors.has('carga_layout')}"
                                                >
                                                <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" v-on:click="cerrarModalCarga" :disabled="cargando"><i class="fa fa-times"></i>Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="procesarLayout()" :disabled="errors.has('carga_layout') || file_carga === null">
                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                <i class="fa fa-upload" v-else ></i> Cargar</button>
                         </div>
                    </form>
                </div>
            </div>
        </div> -->

    </span>
</template>

<script>
    import  ConceptoSelect from "../../cadeco/concepto/Select";
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "contrato-proyectado-create",
        components: {Datepicker, ConceptoSelect},
        data() {
            return {
                es: es,
                fechasDeshabilitadas:{},
                cargando: false,
                fecha: '',
                fecha_cotizacion: '',
                fecha_contrato: '',
                referencia: '',
                areas_subcontratantes:[],
                id_area:'',
                partidas:[],
                partidas_store:[],
                unidades:[],
                edit_concepto_index:'',
                edit_destino_index:'',
                descrip_temporal:'',
                destino_temp:'',
                partida_copia:{
                    destino:'',
                    destino_path:''
                },
                partida_index:'',
                file_carga : null,
                file_carga_name : '',
            }
        },
        mounted(){
            this.getAreaSub();
            this.getUnidades();
        },
        computed: {
        },
        methods: {
            agregarPartida(index){
                if(index === ''){
                    this.partidas.push({
                        clave:'',
                        descripcion:'',
                        descripcion_sin_formato:'',
                        unidad:'',
                        cantidad:'',
                        destino:'',
                        destino_path:'',
                        nivel: 1,
                        es_hoja:true,
                        cantidad_hijos:0,
                    });
                }else{
                    let temp_index = index + 1;
                    while(temp_index in this.partidas && this.partidas[temp_index].nivel >= +this.partidas[index].nivel + 1){
                        temp_index= temp_index + 1;
                    }
                    this.partidas.splice(temp_index, 0, {
                        clave:'',
                        descripcion:'',
                        descripcion_sin_formato:'',
                        unidad:'',
                        cantidad:'',
                        destino:'',
                        destino_path:'',
                        nivel:this.partidas[index].nivel + 1,
                        es_hoja:true,
                        cantidad_hijos:0,
                    });

                this.partidas[index].es_hoja = false;
                this.partidas[index].es_rama = true;
                this.partidas[index].unidad = '';
                this.partidas[index].cantidad = '';
                this.partidas[index].destino = '';
                this.partidas[index].destino_path = '';
                this.partidas[index].cantidad_hijos = this.partidas[index].cantidad_hijos + 1;
                }

            },
            cambiarDestino(){
                this.partidas[this.edit_destino_index].destino = this.destino_temp;
                this.edit_destino_index='';
                this.destino_temp='',
                $(this.$refs.modalDestino).modal('hide')
            },
            cerrarModalDestino(){
                this.destino_temp = '';
                $(this.$refs.modal_destino).modal('hide');
                this.$validator.reset();
            },
            cerrarModalCarga(){
                if(this.$refs.carga_layout.value !== ''){
                    this.$refs.carga_layout.value = '';
                    this.file_carga = null;
                }
                $(this.$refs.modal_carga).modal('hide');
                this.$validator.reset();
            },
            copiar_destino(partida){
                this.partida_copia.destino = partida.destino;
                this.partida_copia.destino_path = partida.destino_path;
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_carga = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            descripcionFormat(i){
                var len = this.partidas[i].descripcion.length + (+this.partidas[i].nivel * 3);
                return this.partidas[i].descripcion.padStart(len, "_")
            },
            descripcionSinFormat(i){
                var len = (this.partidas[i].nivel * 3);
                let lineas = '';
                lineas = lineas.padStart(len, "_");
                return this.partidas[i].descripcion.replace(lineas, '');
            },
            habilitar : function(i, event){
                let nuevo_valor = this.descripcionSinFormat(i);
                this.partidas[i].descripcion = nuevo_valor;
                this.partidas[i].descripcion_sin_formato = nuevo_valor;
                $("#" + event.target.id).removeAttr("readonly");
            },
            deshabilitar : function(i,event){
                let isReadOnly = $("#" + event.target.id).attr("readonly");
                if(isReadOnly !== "readonly"){
                    this.partidas[i].descripcion_sin_formato = this.descripcionSinFormat(i);
                    let nuevo_valor = this.descripcionFormat(i);
                    this.partidas[i].descripcion = nuevo_valor;
                    $("#" + event.target.id).attr("readonly",true);
                }
            },
            editDestino(index){
                this.edit_destino_index = index;
                this.destino_temp = this.partidas[index].destino;
                $(this.$refs.modalDestino).appendTo('body')
                $(this.$refs.modalDestino).modal('show')
            },
            eliminarPartida(index){
                if(this.partidas[index].nivel === 1){
                    this.partidas.splice(index, 1);
                }else{
                    let temp_index = index - 1;
                    while(temp_index in this.partidas && this.partidas[temp_index].nivel == +this.partidas[index].nivel){
                        temp_index= temp_index - 1;
                    }
                    this.partidas[temp_index].cantidad_hijos = this.partidas[temp_index].cantidad_hijos - 1;
                    this.partidas.splice(index, 1);
                    if(this.partidas[temp_index].cantidad_hijos == 0){
                        this.partidas[temp_index].es_hoja = true;
                    }
                }
            },
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            getAreaSub() {
                this.areas_disponibles = [];
                return this.$store.dispatch('configuracion/area-subcontratante/index')
                    .then(data => {
                        if(data.length === 1){
                            this.id_area = data[0].id
                        }
                        this.areas_subcontratantes = data.sort((a, b) => (a.descripcion > b.descripcion) ? 1 : -1);
                    });
            },
            getConcepto(id_concepto) {
                this.cargando = true;
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: id_concepto,
                    params: {
                    }
                })
                .then(data => {
                    let path = data.path.split('->');
                    this.partidas[this.partida_index].destino_path = path[path.length - 2] + ' -> ' + data.descripcion;
                    this.partidas[this.partida_index].destino = data.id;
                })
                .finally(()=> {
                    this.partida_index = '';
                    this.cargando = false;
                    $(this.$refs.modal_destino).modal('hide');

                })
            },
            getLayoutData(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('pagos',  this.file_carga);
                formData.append('nombre_archivo',  this.file_carga_name);
                return this.$store.dispatch('contratos/contrato-proyectado/cargarLayout',{
                        data: formData, config: { params: { _method: 'POST'}}
                })
                .then(data => {
                    this.partidas = data;
                }).finally(() => {
                    this.cargando = false;
                    this.cerrarModalCarga();
                });
            },
            getUnidades() {
                return this.$store.dispatch('cadeco/unidad/index', {
                    params: {sort: 'unidad',  order: 'asc'}
                })
                .then(data => {
                    this.unidades= data.data;
                })
            },
            modalDestino(index) {
                this.partida_index = index;
                this.$validator.reset();
                $(this.$refs.modal_destino).modal('show');
            },
            modalCarga() {
                this.$validator.reset();
                $(this.$refs.modal_carga).modal('show');
            },
            onFileChange(e){
                this.file_carga = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_carga_name = files[0].name;
                this.createImage(files[0]);

            },
            pegar_destino(index){
                this.partidas[index].destino = this.partida_copia.destino;
                this.partidas[index].destino_path = this.partida_copia.destino_path;
                this.$forceUpdate();
            },
            procesarLayout(){
                if(this.partidas.length > 0){
                    swal({
                    title: "Cargar Layout Contrato Proyectado",
                    text: "El contrato ya tiene partidas agregadas, si continua se reemplazarán por las contenidas en el layout.",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: true,
                        }
                    }})
                    .then((value) => {
                        if (value) {
                            this.getLayoutData();
                        }else{
                            this.cerrarModalCarga();
                        }

                    });
                }else{
                    this.getLayoutData();
                }
            },
            salir(){
                if(this.partidas.length > 0){
                    swal({
                    title: "Cerrar Registro Contrato Proyectado",
                    text: "El contrato tiene partidas agregadas, si continua se perderan los cambios.",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Continuar',
                            closeModal: true,
                        }
                    }})
                    .then((value) => {
                        if (value) {
                            this.$router.push({name: 'proyectado'});
                        }

                    });
                }else{
                    this.$router.push({name: 'proyectado'});
                }
            },
            setFechasDeshabilitadas(fecha){
                this.fechasDeshabilitadas.to = fecha;
            },
            store() {
                let datos = {
                    'fecha':this.fecha,
                    'cumplimineto':this.$data.fecha_cotizacion,
                    'vencimiento':this.$data.fecha_contrato,
                    'referencia':this.$data.referencia,
                    'id_area_subcontratante':this.$data.id_area,
                    'contratos':this.$data.partidas
                };
                return this.$store.dispatch('contratos/contrato-proyectado/store',  datos)
                    .then((data) => {
                        this.$router.push({name: 'proyectado'});
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        let tam_desc = false;
                        let ip = 0;
                        this.partidas.forEach(partida => {
                            if(partida.descripcion_sin_formato.length > 255){
                                tam_desc = true;
                            }
                            ip++;
                        });
                        if(tam_desc){
                            swal('Atención', 'La longitud de la descripción de una partida es mayor a la permitida de 255 caracteres.', 'warning');
                        }
                        else if(this.partidas.length === 0){
                            swal('Atención', 'Debe agregar al menos una partida', 'warning');
                        }else if(this.validarFechas()){
                            swal('Atención', 'La fecha de contratación no debe ser anterior a la fecha de cotización', 'warning');
                        }else{
                            this.store();
                        }

                    }
                });
            },
            validarFechas(){
                var f_cotizacion = Date.parse(this.fecha_cotizacion);
                var f_contrato = Date.parse(this.fecha_contrato);
                return f_contrato < f_cotizacion;
            },
        },
        watch: {
            destino_temp(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.getConcepto(value);
                }
            },
            fecha_cotizacion(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.setFechasDeshabilitadas(value);
                    this.$forceUpdate();
                }
            }
        },
    }
</script>
