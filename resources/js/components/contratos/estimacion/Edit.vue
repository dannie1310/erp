<template>
    <span>
        <div class="row" v-if="estimacion">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h6 class="card-title">Subcontrato</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-group row">
								<label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
								<div class="col-sm-10">
									<input
                                        readonly
                                        name="fecha"
                                        v-validate="{required: true}"
                                        data-vv-as="Fecha"
                                        :class="{'is-invalid': errors.has('fecha')}"
                                        v-model="fecha"
                                        type="date"
                                        class="form-control"
                                        id="fecha"
                                        placeholder="Fecha">
									<div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Objeto</label>
								<div class="col-sm-10">
									{{ estimacion.subcontrato.referencia }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Contratista</label>
								<div class="col-sm-10">
									{{ estimacion.razon_social }}
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Observaciones</label>
								<div class="col-sm-10">
									<textarea
                                        name="observaciones"
                                        id="observaciones"
                                        class="form-control"
                                        v-model="estimacion.observaciones"
                                    ></textarea>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="card" v-if="estimacion">
					<div class="card-header">
						<h6 class="card-title">Periodo de Estimación</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label>Inicio</label>
									<input
                                        name="fecha_inicio"
                                        v-validate="{required: true}"
                                        data-vv-as="Inicio"
                                        :class="{'is-invalid': errors.has('fecha_inicio')}"
                                        v-model="fecha_inicio"
                                        type="date"
                                        class="form-control"
                                        id="fecha_inicio"
                                        placeholder="Inicio">
									<div class="invalid-feedback" v-show="errors.has('fecha_inicio')">{{ errors.first('fecha_inicio') }}</div>

								</div>
								<div class="form-group col-md-6">
									<label for="inicio">Término</label>
									<input
                                        name="fecha_fin"
                                        v-validate="{required: true}"
                                        data-vv-as="Término"
                                        :class="{'is-invalid': errors.has('fecha_fin')}"
                                        v-model="fecha_fin"
                                        type="date"
                                        class="form-control"
                                        id="fecha_fin"
                                        placeholder="Término">
									<div class="invalid-feedback" v-show="errors.has('fecha_fin')">{{ errors.first('fecha_fin') }}</div>

								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

        <div class="card" v-if="estimacion">
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
        <div class="card" v-if="estimacion">
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
					<tbody v-for="(concepto, i) in estimacion.subcontrato.partidas">
                        <tr v-if="concepto.para_estimar == 0">
                            <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                            <td :title="concepto.descripcion"><b>{{concepto.descripcion}}</b></td>
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
						    <td :title="concepto.item.clave">  {{ concepto.item.clave }}</td>
                            <td :title="concepto.item.descripcion_concepto">  {{concepto.item.descripcion_concepto}}</td>
                            <td class="centrado">{{concepto.item.unidad}}</td>
                            <td style="display: none" class="numerico contratado">{{ concepto.item ? concepto.item.cantidad_subcontrato : '' }}</td>
                            <td style="display: none" class="numerico contratado">{{ concepto.item ? concepto.item.precio_unitario_subcontrato : '' }}</td>
                            <td style="display: none" class="numerico avance-volumen">{{ concepto.item ? concepto.item.cantidad_estimada_anterior : '' }}</td>
                            <td style="display: none" class="numerico avance-volumen">{{ concepto.item ? concepto.item.cantidad_estimada_total : '' }}</td>
                            <td style="display: none" class="numerico avance-volumen">{{ concepto.item ? concepto.item.porcentaje_avance : '' }}</td>
                            <td style="display: none" class="numerico avance-importe"></td>
                            <td style="display: none" class="numerico avance-importe">{{ concepto.item ? concepto.item.importe_estimado_anterior : '' }}</td>
                            <td style="display: none" class="numerico saldo">{{ concepto.item ? concepto.item.cantidad_por_estimar : '' }}</td>
                            <td style="display: none" class="numerico saldo">{{ concepto.item ? concepto.item.importe_por_estimar : '' }}</td>
                            <td class="editable-cell numerico">
                                <input v-on:change="changeCantidad(concepto.item)" class="text" v-model="concepto.item.cantidad_estimacion"
                                       :name="'cantidad_estimacion' + i"
                                       v-validate="{max_value: parseFloat(concepto.item.cantidad_por_estimar)}"
                                       :class="{'is-invalid': errors.has('cantidad_estimacion' + i)}" />
                            </td>
                            <td class="editable-cell numerico">
                                <input v-on:change="changePorcentaje(concepto.item)" class="text" v-model="concepto.item.porcentaje_estimado" />
                            </td>
                            <td class="numerico">{{ concepto.item.precio_unitario_subcontrato}}</td>
                            <td class="editable-cell numerico">
                                <input v-on:change="changeImporte(concepto.item)" class="text" v-model="concepto.item.importe_estimacion" />
                            </td>
                            <td style="display: none" class="destino" :title="concepto.item.destino_path">{{ concepto.item.destino_path }}</td>
                        </tr>
                    </tbody>
				</table>
			</div>
        </div>
    </span>
</template>
<script>
import DeductivaEdit from './deductivas/Edit'
    export default {
        name: "estimacion-edit",
        components: {DeductivaEdit},
        data() {
            return {
                cargando: true,
                conceptos: null,
                columnas: [],
                fecha_inicio: '',
                fecha_fin: '',
                fecha: ''
            }
        },
        mounted() {
            this.cargando = true;
            this.find();
        },
        methods: {
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
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            find() {
                this.$store.commit('contratos/estimacion/SET_ESTIMACION', null);
                return this.$store.dispatch('contratos/estimacion/ordenarConceptos', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('contratos/estimacion/SET_ESTIMACION', data);
                    this.fecha_inicio = data.fecha_inicial
                    this.fecha_fin = data.fecha_final
                    this.fecha = data.fecha
                    this.cargando = false;
                })
            },

        },
        computed: {
            estimacion() {
                return this.$store.getters['contratos/estimacion/currentEstimacion']
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
