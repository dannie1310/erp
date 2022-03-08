<template>
    <span>
        <div class="row" v-if="partidas">
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
                            <br />
                            <div class="row">
                                <div  class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-sm">
                                            <thead>
                                            <tr>
                                                <th class="c120">Clave</th>
                                                <th >Descripción</th>
                                                <th class="c150">Unidad</th>
                                                <th class="c150">Cantidad</th>
                                                <th >Destinos</th>
                                            </tr>
                                            </thead>
                                           <tbody>
                                            <tr v-for="(partida, i) in partidas">
                                                <td>{{partida.clave}}</td>
                                                <td>{{partida.descripcion}}</td>
                                                <td>{{partida.unidad}} </td>
                                                <td>{{partida.cantidad}} </td>
                                                <td>{{partida.destino_path}}</td>
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
    </span>
</template>

<script>
    import  ConceptoSelect from "../../cadeco/concepto/Select";
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "contrato-proyectado-create",
        components: {Datepicker, ConceptoSelect},
        props: ['partidas'],
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
            }
        },
        mounted(){
            this.getAreaSub();
        },
        computed: {
        },
        methods: {
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
                    'contratos':this.partidas
                };
                return this.$store.dispatch('contratos/contrato-proyectado/store',  datos)
                    .then((data) => {
                        this.$router.push({name: 'proyectado'});
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.partidas.length === 0){
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
            fecha_cotizacion(value){
                if(value !== '' && value !== null && value !== undefined){
                    this.setFechasDeshabilitadas(value);
                    this.$forceUpdate();
                }
            }
        },
    }
</script>
