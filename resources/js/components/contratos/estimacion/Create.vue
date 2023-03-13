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
            <div class="row">
                <div class="col-md-6">
                    <div class="card" v-if="subcontrato">
                        <div class="card-header">
                            <h6 class="card-title">Subcontrato</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group row">
                                    <label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
                                    <div class="col-sm-10">
                                        <datepicker v-model = "fecha"
                                                    name = "fecha"
                                                    :format = "formatoFecha"
                                                    :language = "es"
                                                    :bootstrap-styling = "true"
                                                    class = "form-control"
                                                    :disabled-dates="fechasDeshabilitadas"
                                                    v-validate="{required: true}"
                                                    :class="{'is-invalid': errors.has('fecha')}"
                                        ></datepicker>
                                        <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Objeto</label>
                                    <div class="col-sm-10">
                                        {{ subcontrato.referencia }}
                                    </div>
                                </div>
                                <div class="form-group row" v-if="subcontrato.empresa">
                                    <label class="col-sm-2 col-form-label">Contratista</label>
                                    <div class="col-sm-10">
                                        {{ subcontrato.empresa.razon_social }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Observaciones</label>
                                    <div class="col-sm-10">
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                        ></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card" v-if="subcontrato">
                        <div class="card-header">
                            <h6 class="card-title">Periodo de Estimación</h6>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Inicio</label>
                                        <datepicker v-model = "fecha_inicio"
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
                                    <div class="form-group col-md-6">
                                        <label class="col-form-label">Término</label>
                                        <datepicker v-model = "fecha_fin"
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" v-if="subcontrato">
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
            <div class="card" v-if="conceptos">
                <div class="card-body table-responsive">
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
                        <tbody v-for="(concepto, i) in conceptos">
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
                                <td style="display: none" class="numerico avance-volumen">{{ parseFloat(concepto.porcentaje_avance).formatMoney(3) }}</td>
                                <td style="display: none" class="numerico avance-importe"></td>
                                <td style="display: none" class="numerico avance-importe">{{ parseFloat(concepto.importe_estimado_anterior).formatMoney(4) }}</td>
                                <td style="display: none" class="numerico saldo">{{  toFix(concepto.cantidad_por_estimar,2) }}</td>
                                <td style="display: none" class="numerico saldo">{{ parseFloat(concepto.importe_por_estimar).formatMoney(4) }}</td>
                                <td class="editable-cell numerico">
                                    <input v-on:change="changeCantidad(concepto)"
                                           class="text"
                                           v-model="concepto.cantidad_estimacion"
                                           :name="`cantidadEstimacion[${concepto.id}]`"
                                           v-validate="{max_value: parseFloat(concepto.cantidad_por_estimar).toFixed(3)}"
                                           :class="{'is-invalid': errors.has(`cantidadEstimacion[${concepto.id}]`)}" />
                                     <div class="invalid-feedback" v-show="errors.has(`cantidadEstimacion[${concepto.id}]`)">{{ errors.first(`cantidadEstimacion[${concepto.id}]`) }}</div>
                                </td>
                                <td class="editable-cell numerico">
                                    <input v-on:change="changePorcentaje(concepto)"
                                           v-validate="{max_value: parseFloat(100 - parseFloat(concepto.porcentaje_avance).toFixed(3)).toFixed(3) }"
                                           class="text" 
                                           :name="`porcentaje[${concepto.id}]`"
                                           v-model="concepto.porcentaje_estimado"
                                           :class="{'is-invalid': errors.has(`porcentaje[${concepto.id}]`)}" />
                                     <div class="invalid-feedback" v-show="errors.has(`porcentaje[${concepto.id}]`)">{{ errors.first(`porcentaje[${concepto.id}]`) }}</div>
                                </td>
                                <td class="numerico">{{ concepto.precio_unitario_subcontrato_format}}</td>
                                <td class="editable-cell numerico">
                                    <input v-on:change="changeImporte(concepto)"
                                           class="text"
                                           :name="`importe[${concepto.id}]`"
                                           v-validate="{max_value: parseFloat(concepto.importe_por_estimar).toFixed(2)}"
                                           v-model="concepto.importe_estimacion"
                                           :class="{'is-invalid': errors.has(`importe[${concepto.id}]`)}" />
                                     <div class="invalid-feedback" v-show="errors.has(`importe[${concepto.id}]`)">{{ errors.first(`importe[${concepto.id}]`) }}</div>
                                </td>
                                <td style="display: none" class="destino" :title="concepto.destino_path">{{ concepto.destino_path }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
	</span>
</template>

<script>
	import {ModelListSelect} from 'vue-search-select';
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "estimacion-create",
        components: {ModelListSelect, Datepicker, es},
        props: ['id'],
        data() {
            return {
                es:es,
                id_subcontrato: '',
                subcontrato: null,
                conceptos: null,
                cargando: false,
				columnas: [],
				fecha_inicio: '',
				fecha_fin: '',
				observaciones: '',
                fecha: '',
                fechasDeshabilitadas:{},
                fecha_hoy : '',
            }
        },

		mounted() {
        	this.fecha_inicio = new Date()
        	this.fecha_fin = new Date()
        	this.fecha = new Date()
            this.fecha_hoy = new Date()
            this.fechasDeshabilitadas.from= new Date();
        	this.getSubcontrato();
        },

		methods: {
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            changeCantidad(concepto) {
                concepto.porcentaje_estimado = this.toFix(((concepto.cantidad_estimacion / concepto.cantidad_subcontrato) * 100),3);
                concepto.importe_estimacion = this.toFix((concepto.cantidad_estimacion * concepto.precio_unitario_subcontrato),2);
            },
            changePorcentaje(concepto) {
                concepto.cantidad_estimacion = this.toFix(((concepto.cantidad_subcontrato * concepto.porcentaje_estimado) / 100),3);
                concepto.importe_estimacion = this.toFix((concepto.cantidad_estimacion * concepto.precio_unitario_subcontrato),2);
            },
            changeImporte(concepto) {
                concepto.cantidad_estimacion = this.toFix((concepto.importe_estimacion / concepto.precio_unitario_subcontrato),3);
                concepto.porcentaje_estimado = this.toFix(((concepto.cantidad_estimacion / concepto.cantidad_subcontrato) * 100),3);
            },
            getSubcontrato()
            {
                this.cargando = true;
                this.subcontrato = null;
                this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: { include: 'empresa' }
                })
                .then(data => {
                    this.subcontrato = data;
                    this.observaciones = data.Observaciones;
                    this.getConceptosSubcontrato();
                })
            },
            getConceptosSubcontrato()
            {
                this.conceptos = null;
                this.$store.dispatch('contratos/subcontrato/ordenarConceptos', {
                    id: this.id
                }).then(data => {
                    this.conceptos = data.partidas;
                }).finally(() => {
                    this.cargando = false;
                })
            },
			validate() {
				this.$validator.validate().then(result => {
					if (result) {
                        if(moment(this.fecha_fin).format('YYYY/MM/DD') < moment(this.fecha_inicio).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha de inicio no puede ser posterior a la fecha de término.', 'error')
                        }
                        else if(moment(this.fecha_hoy).format('YYYY/MM/DD') < moment(this.fecha).format('YYYY/MM/DD'))
                        {
                            swal('¡Error!', 'La fecha no puede ser mayor a la fecha actual.', 'error')
                        }
                        else {
                            this.store()
                        }
					}
				});
			},

			store() {
        		var conceptos = this.getConceptos();
        		if(conceptos.length > 0) {
					return this.$store.dispatch('contratos/estimacion/store', {
						id_antecedente: this.id,
                        fecha: moment(this.fecha).format('YYYY-MM-DD'),
						cumplimiento: moment(this.fecha_inicio).format('YYYY-MM-DD'),
						vencimiento:  moment(this.fecha_fin).format('YYYY-MM-DD'),
						observaciones: this.observaciones,
						conceptos: conceptos
					})
							.then(data=> {
								this.$router.push({name: 'estimacion-index'});
								this.$router.push({name: 'estimacion'});

							})
				} else {
        		    swal('','Debe estimar al menos un concepto','warning');
				}
			},

			getConceptos() {
        		var conceptos = [];
                for (var key in this.conceptos) {
                    var obj = this.conceptos[key];
                    if( typeof obj.para_estimar === 'undefined') {
                        if (parseFloat(obj.cantidad_estimacion) !== 0) {
                            conceptos.push({
                                item_antecedente: obj.id_concepto,
                                id_concepto: obj.id_destino,
                                importe: obj.importe_estimacion,
                                cantidad: obj.cantidad_estimacion
                            })
                        }
                    }
                }
				return conceptos;
			},
            toFix(num, fixed) {
                fixed = fixed || 0;
                fixed = Math.pow(10, fixed);
                return Math.floor(num * fixed) / fixed;
            },
            salir()
            {
                this.$router.go(-1);
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
			conceptos: {
            	handler() {
            		setTimeout(() => {
						this.$validator.validate()
					}, 500);
				},
				deep: true
			}
        }
    }
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
