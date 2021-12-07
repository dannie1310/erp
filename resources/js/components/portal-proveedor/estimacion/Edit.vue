<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" v-if="cargando">
                            <div class="row" >
                                <div class="col-md-12">
                                    <div class="spinner-border text-success" role="status">
                                       <span class="sr-only">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="!cargando">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <encabezado v-bind:estimacion="estimacion" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group error-content">
                                            <label class="col-form-label">Inicio de Estimación</label>
                                            <datepicker v-model = "estimacion.fecha_cumplimiento"
                                                        name = "fecha_inicio"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha_inicio')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha_inicio')">{{ errors.first('fecha_inicio') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group error-content">
                                            <label class="col-form-label">Fin de Estimación</label>
                                            <datepicker v-model = "estimacion.fecha_vencimiento"
                                                        name = "fecha_fin"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha_fin')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha_fin')">{{ errors.first('fecha_fin') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" value="contratado" id="contratado">
                                                <label class="form-check-label" for="contratado">Contratado</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="avance-volumen" value="avance-volumen">
                                                <label class="form-check-label" for="avance-volumen">Avance Volumen</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="avance-importe" value="avance-importe">
                                                <label class="form-check-label" for="avance-importe">Avance Importe</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="saldo" value="saldo">
                                                <label class="form-check-label" for="saldo">Saldo</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input v-model="columnas" class="form-check-input" type="checkbox" id="destino" value="destino">
                                                <label class="form-check-label" for="destino">Destino</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row" v-if="!cargando">
                                        <div class="table-responsive">
                                            <table id="tabla-conceptos">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Clave</th>
                                                        <th rowspan="2">Concepto</th>
                                                        <th rowspan="2">UM</th>
                                                        <th style="display: none" colspan="2" class="contratado">Contratado</th>
                                                        <th style="display: none" colspan="3" class="avance-volumen">Avance Volumen</th>
                                                        <th style="display: none" colspan="2" class="avance-importe">Avance Importe</th>
                                                        <th style="display: none" colspan="2" class="saldo">Saldo</th>
                                                        <th colspan="4">Esta Estimación</th>
                                                        <th style="display: none" class="destino">Distribución</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="display: none" class="contratado">Volumen</th>
                                                        <th style="display: none" class="contratado">P.U.</th>
                                                        <th style="display: none" class="avance-volumen">Anterior</th>
                                                        <th style="display: none" class="avance-volumen">Acumulado</th>
                                                        <th style="display: none" class="avance-volumen">%</th>
                                                        <th style="display: none" class="avance-importe">Anterior</th>
                                                        <th style="display: none" class="avance-importe">Acumulado</th>
                                                        <th style="display: none" class="saldo">Volumen</th>
                                                        <th style="display: none" class="saldo">Importe</th>
                                                        <th>Volumen</th>
                                                        <th>%</th>
                                                        <th>P.U.</th>
                                                        <th>Importe</th>
                                                        <th style="display: none" class="destino">Destino</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-for="(concepto, i) in estimacion.subcontrato.partidas">
                                                    <tr v-if="concepto.para_estimar == 0">
                                                        <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                                        <td :title="concepto.descripcion">
                                                            <span v-for="n in concepto.nivel">&nbsp;</span>
                                                            <b>{{concepto.descripcion}}</b></td>
                                                        <td></td>
                                                        <td style="display: none" class="numerico contratado"/>
                                                        <td style="display: none" class="numerico contratado"/>
                                                        <td style="display: none" class="numerico avance-volumen"/>
                                                        <td style="display: none" class="numerico avance-volumen"/>
                                                        <td style="display: none" class="numerico avance-volumen"/>
                                                        <td style="display: none" class="numerico avance-importe"/>
                                                        <td style="display: none" class="numerico avance-importe"/>
                                                        <td style="display: none" class="numerico saldo"/>
                                                        <td style="display: none" class="numerico saldo"/>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="display: none" class="destino"/>
                                                    </tr>
                                                    <tr v-else>
                                                        <td :title="concepto.clave">{{ concepto.clave }}</td>
                                                        <td :title="concepto.descripcion_concepto">
                                                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                            {{concepto.descripcion_concepto}}
                                                        </td>
                                                        <td class="centrado">{{concepto.unidad}}</td>
                                                        <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico contratado">{{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-volumen"></td>
                                                        <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico avance-importe"></td>
                                                        <td style="display: none" class="numerico avance-importe">{{ parseFloat(concepto.importe_estimado_anterior).formatMoney(4) }}</td>
                                                        <td style="display: none" class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                                                        <td style="display: none" class="numerico saldo">{{ parseFloat(concepto.importe_por_estimar).formatMoney(4) }}</td>
                                                        <td class="editable-cell numerico">
                                                            <input v-on:change="changeCantidad(concepto)"
                                                                   class="text"
                                                                   v-model="concepto.cantidad_estimacion"
                                                                   :name="`cantidadEstimacion[${concepto.id}]`"
                                                                   v-validate="{max_value: parseFloat(concepto.cantidad_por_estimar).toFixed(2)}"
                                                                   :class="{'is-invalid': errors.has(`cantidadEstimacion[${concepto.id}]`)}" />
                                                        </td>
                                                        <td class="editable-cell numerico">
                                                            <input v-on:change="changePorcentaje(concepto)"
                                                                   v-validate="{max_value: parseFloat(100 - parseFloat(concepto.porcentaje_avance).toFixed(2)).toFixed(2) }"
                                                                   class="text"
                                                                   :name="`porcentaje[${concepto.id}]`"
                                                                   v-model="concepto.porcentaje_estimado"
                                                                   :class="{'is-invalid': errors.has(`porcentaje[${concepto.id}]`)}" />
                                                        </td>
                                                        <td class="numerico">{{ concepto.precio_unitario_subcontrato_format }}</td>
                                                        <td class="editable-cell numerico">
                                                            <input v-on:change="changeImporte(concepto)"
                                                                   class="text"
                                                                   :name="`importe[${concepto.id}]`"
                                                                   v-validate="{max_value: parseFloat(concepto.importe_por_estimar).toFixed(2)}"
                                                                   v-model="concepto.importe_estimacion"
                                                                   :class="{'is-invalid': errors.has(`importe[${concepto.id}]`)}" />
                                                        </td>
                                                        <td style="display: none" class="destino" :title="concepto.destino_path">{{ concepto.destino_path }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <br>
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
                                                v-model="estimacion.observaciones"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                            ></textarea>
                                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar
                        </button>
                        <button type="button" class="btn btn-primary" @click="validate" :disabled="cargando">
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Encabezado from './partials/EncabezadoEstimacion';
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "estimacion-edit",
        props: ["id", "base"],
        components: {Encabezado, Datepicker, es},
        data() {
            return {
                es:es,
                cargando: true,
                columnas: [],
                estimacion: [],
                fechasDeshabilitadas: {}
            };
        },
        mounted() {
            this.fechasDeshabilitadas.from = new Date();
            if (this.base == undefined) {
                this.salir();
            } else {
                this.find();
            }
        },
        methods: {
            formatoFecha(date) {
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                return this.$store.dispatch('contratos/estimacion/ordenarConceptosProveedor', {
                    id: this.id,
                    base: this.base
                }).then(data => {
                    this.estimacion = data;
                }).finally(() => {
                    this.cargando = false;
                })
            },
            salir() {
                this.$router.push({name: 'estimacion-proveedor'});
            },
            changeCantidad(concepto) {
                concepto.porcentaje_estimado = ((concepto.cantidad_estimacion / concepto.cantidad_subcontrato) * 100).toFixed(2);
                concepto.importe_estimacion = (concepto.cantidad_estimacion * concepto.precio_unitario_subcontrato).toFixed(4);
            },
            changePorcentaje(concepto) {
                concepto.cantidad_estimacion = ((concepto.cantidad_subcontrato * concepto.porcentaje_estimado) / 100).toFixed(2);
                concepto.importe_estimacion = (concepto.cantidad_estimacion * concepto.precio_unitario_subcontrato).toFixed(4);
            },
            changeImporte(concepto) {
                concepto.cantidad_estimacion = (concepto.importe_estimacion / concepto.precio_unitario_subcontrato).toFixed(2);
                concepto.porcentaje_estimado = ((concepto.cantidad_estimacion / concepto.cantidad_subcontrato) * 100).toFixed(2);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(moment(this.estimacion.fecha_final).format('YYYY/MM/DD') < moment(this.estimacion.fecha_inicial).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de término.', 'error')
                        }else{
                            this.update()
                        }
                    }
                });
            },
            update() {
                this.estimacion.base =  this.base;
                return this.$store.dispatch('contratos/estimacion/updateProveedor', {
                    id: this.id,
                    data: this.estimacion
                })
                    .then((data) => {
                        this.$router.push({name: 'estimacion-proveedor'});
                    })
            },
        },
        watch: {
            columnas(val) {
                $('.contratado').css('display', 'none');
                $('.avance-volumen').css('display', 'none');
                $('.avance-importe').css('display', 'none');
                $('.saldo').css('display', 'none');
                $('.destino').css('display', 'none');

                val.forEach(v => {
                    $('.' + v).removeAttr('style')
                })
            },
        }
    };
</script>

<style scoped>
    table#tabla-conceptos {
        word-wrap: unset;
        width: 100%;
        background-color: white;
        border-color: transparent;
        border-collapse: collapse;
        clear: both;
    }

    table thead th
    {
        padding: 0.2em;
        border: 1px solid #666;
        background-color: #333;
        color: white;
        font-weight: normal;
        overflow: hidden;
        text-align: center;
    }

    table thead th {
        text-align: center;
    }
    table tbody tr
    {
        border-width: 0 1px 1px 1px;
        border-style: none solid solid solid;
        border-color: white #CCCCCC #CCCCCC #CCCCCC;
    }
    table tbody td,
    table tbody th
    {
        border-right: 1px solid #ccc;
        color: #242424;
        line-height: 20px;
        overflow: hidden;
        padding: 1px 5px;
        text-align: left;
        text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        -ms-text-overflow: ellipsis;
        white-space: nowrap;
    }

    table col.clave { width: 120px; }
    table col.icon { width: 25px; }
    table col.monto { width: 115px; }
    table col.pct { width: 60px; }
    table col.unidad { width: 80px; }
    table col.clave  {width: 100px; }

    table tbody td input.text
    {
        border: none;
        padding: 0;
        margin: 0;
        width: 100%;
        background-color: transparent;
        font-family: inherit;
        font-size: inherit;
        font-weight: bold;
    }

    table tbody .numerico
    {
        text-align: right;
        padding-left: 0;
        white-space: normal;
    }

    .text.is-invalid {
        color: #dc3545;
    }

    table tbody td input.text {
        text-align: right;
    }
</style>

