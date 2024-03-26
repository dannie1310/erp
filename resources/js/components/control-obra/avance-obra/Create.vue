<template>
    <span>
        <div class="card">
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="fecha">Fecha:</label>
                                        </div>
                                        <div class="col-md-10">
                                            <datepicker v-model = "fecha"
                                                        id="fecha"
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
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header ui-sortable-handle" style="cursor: move;">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-pie mr-1"></i>Periodo de Avance
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="fecha">Inicio:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <datepicker v-model = "fechaInicio"
                                                            id="fechaInicio"
                                                            name = "fechaInicio"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :disabled-dates="fechasDeshabilitadas"
                                                            :class="{'is-invalid': errors.has('fechaInicio')}"
                                                ></datepicker>
                                                <div class="invalid-feedback" v-show="errors.has('fechaInicio')">{{ errors.first('fechaInicio') }}</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="fechaTermino">Término:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <datepicker v-model = "fechaTermino"
                                                            id="fechaTermino"
                                                            name = "fechaTermino"
                                                            :format = "formatoFecha"
                                                            :language = "es"
                                                            :bootstrap-styling = "true"
                                                            class = "form-control"
                                                            v-validate="{required: true}"
                                                            :disabled-dates="fechasDeshabilitadas"
                                                            :class="{'is-invalid': errors.has('fechaTermino')}"
                                                ></datepicker>
                                                <div class="invalid-feedback" v-show="errors.has('fechaTermino')">{{ errors.first('fechaTermino') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-2">
                            <label>Concepto: </label>
                        </div>
                        <div class="col-md-10">
                            <concepto-select
                                name="id_concepto"
                                data-vv-as="Concepto"
                                id="id_concepto"
                                v-model="id_concepto"
                                :error="errors.has('id_concepto')"
                                ref="conceptoSelect"
                                :disableBranchNodes="false"
                                :placeholder="'Seleccione el concepto'"
                            ></concepto-select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                        </div>
                    </div>
                    <br />
                    <div class="row" v-if="cargando_hijos">
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="hijos">
                        <div  class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered " id="tabla-resumen-monedas">
                                    <thead>
                                        <tr>
                                             <th class="index_corto" rowspan="2">#</th>
                                            <th rowspan="2">Clave</th>
                                            <th rowspan="2">Concepto</th>
                                            <th rowspan="2">Unidad</th>
                                            <th colspan="3">Cantidad</th>
                                            <th rowspan="2">Precio Venta</th>
                                            <th rowspan="2">Monto Avance</th>
                                            <th rowspan="2">Cantidad Actual</th>
                                            <th rowspan="2">Monto Actual</th>
                                            <th rowspan="2">Cumplido</th>
                                        </tr>
                                        <tr>
                                            <th>Presupuesto</th>
                                            <th>Anterior</th>
                                            <th>Avance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(partida,i) in hijos.data">
                                            <td style="text-align:center; vertical-align:inherit;">{{partida.i}}</td>
                                            <td> {{partida.nivel}}</td>
                                            <td v-if="partida.concepto_medible != 3"><b>{{partida.descripcion}}</b></td>
                                            <td v-else> {{partida.descripcion}}</td>
                                            <td style="text-align:center;">{{partida.unidad}}</td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.cantidad_presupuestada}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.cantidad_anterior_format}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3">
                                                <input type="text"
                                                       class="form-control"
                                                       v-on:keyup="getEditarCantidades(partida)"
                                                       :name="`avance[${i}]`"
                                                       data-vv-as="Avance"
                                                       v-validate="{required: true, regex: /^-?[0-9]\d*(\.\d{0,6})?$/}"
                                                       :class="{'is-invalid': errors.has(`avance[${i}]`)}"
                                                       v-model="partida.avance"
                                                       style="text-align: right" />
                                                <div class="invalid-feedback" v-show="errors.has(`avance[${i}]`)">{{ errors.first(`avance[${i}]`) }}</div>
                                            </td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.precio_venta}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.monto_avance}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.cantidad_actual}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3" style="text-align:right;">{{partida.monto_actual}}</td>
                                            <td v-else></td>
                                            <td v-if="partida.concepto_medible == 1 || partida.concepto_medible == 3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" id="cumplido" :name="`cumplido[${i}]`" v-model="partida.cumplido">
                                                    <label class="form-check-label" for="cumplido">Si</label>
                                                </div>
                                            </td>
                                            <td v-else></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-2">
                            <label for="observaciones" class="col-form-label">Observaciones: </label>
                        </div>
                        <div class="col-md-10">
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
                        Regresar</button>
                    <button type="submit" :disabled="hijos == null" class="btn btn-primary">
                        Continuar
                        <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </span>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    import {ModelListSelect} from 'vue-search-select';
    import ConceptoSelect from "../../cadeco/concepto/Select";
    export default {
        name: "create-avance-obra",
        components: {ModelListSelect, ConceptoSelect, Datepicker, es},
        data() {
            return {
                cargando: false,
                cargando_hijos: false,
                id_concepto: '',
                hijos: null,
                fecha: '',
                fechaInicio: '',
                fechaTermino: '',
                es:es,
                fechasDeshabilitadas:{},
                observaciones: ''
            }
        },
        mounted() {
            this.fecha = new Date();
            this.fechaInicio = new Date();
            this.fechaTermino = new Date();
            this.$validator.reset();
        },
        methods : {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            salir()
            {
                 this.$router.push({name: 'avance-obra'});
            },
            getConceptos() {
                this.hijos = null;
                this.cargando_hijos = true;
                return this.$store.dispatch('cadeco/concepto/hijosMedibles', {
                    id: this.id_concepto,
                    params: {
                    }
                })
                .then(data => {
                    this.cargando_hijos = false;
                    this.hijos = data;
                })
                    .catch(error => {
                        this.cargando_hijos = false;
                    });
            },
            validate() {
                var partida = false
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.hijos == null)
                        {
                           swal('¡Error!', 'Debe seleccionar otro concepto.', 'error')
                        }
                        else {
                            for (const k in this.hijos.data) {
                                if (this.hijos['data'][k]['concepto_medible'] == 1 || this.hijos['data'][k]['concepto_medible'] == 3) {
                                    if (parseFloat(this.hijos['data'][k]['avance']) != 0) {
                                        partida = true;
                                    }
                                }
                            }
                        }
                        if(partida) {
                            this.store()
                        }else{
                            swal('¡Error!', 'Debe tener cantidad en algún concepto para generar el avance.', 'error')
                        }
                    }
                });
            },
            store() {
                var datos = {};
                datos['id_concepto_padre'] = this.id_concepto;
                datos['fecha'] = this.fecha;
                datos['fecha_inicio'] = this.fechaInicio;
                datos['fecha_termino'] = this.fechaTermino;
                datos['observaciones'] = this.observaciones;
                datos['conceptos'] = this.hijos
                return this.$store.dispatch('controlObra/avance-obra/store', datos)
                    .then((data) => {
                        this.salir();
                    });
            },
            getEditarCantidades(partida)
            {
                partida['cantidad_actual'] = parseFloat(partida['cantidad_anterior'] + parseFloat(partida['avance'])).formatMoney(4, '.', ',');
                partida['monto_actual'] =  parseFloat((partida['cantidad_anterior'] + parseFloat(partida['avance'])) * partida['precio_venta']).formatMoney(4, '.', ',');
            }
        },
        watch: {
            id_concepto(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.getConceptos();
                }
            },
        }
    }
</script>

<style scoped>
    table#tabla-resumen-monedas, table.tabla {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }
</style>
