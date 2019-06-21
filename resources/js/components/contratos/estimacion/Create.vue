<template>
    <span>
        <div class="card">
			<div class="card-body">
				<subcontrato-select v-model="id_subcontrato"></subcontrato-select>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="card" v-if="subcontrato">
					<div class="card-header">
						<h3 class="card-title">Subcontrato</h3>
					</div>
					<div class="card-body">
						<form>
							<div class="form-group row">
								<label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
								<div class="col-sm-10">
									<input
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
								<label for="staticEmail" class="col-sm-2 col-form-label">Objeto</label>
								<div class="col-sm-10">
									{{ subcontrato.referencia }}
								</div>
							</div>
							<div class="form-group row" v-if="subcontrato.empresa">
								<label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
								<div class="col-sm-10">
									{{ subcontrato.empresa.razon_social }}
								</div>
							</div>
							<div class="form-group row">
								<label for="inputPassword" class="col-sm-2 col-form-label">Observaciones</label>
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
						<h3 class="card-title">Periodo de Estimación</h3>
					</div>
					<div class="card-body">
						<form>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inicio">Inicio</label>
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
					<tbody>
					<tr v-for="(concepto, i) in conceptos" :data-id="concepto.IDConceptoContrato" :data-iddestino="concepto.EsActividad ? concepto.IDConceptoDestino : null">
						<td :title="concepto.clave">{{ concepto.clave }}</td>
						<td :title="concepto.Descripcion">
							<span v-for="n in parseInt(concepto.NumeroNivel)">- </span>
							<span v-if="concepto.EsActividad == '1'">
								{{ concepto.Descripcion }}
							</span>
							<span v-else>
								<b>{{ concepto.Descripcion}}</b>
							</span>
						</td>
						<td class="centrado">{{ concepto.Unidad }}</td>
						<td style="display: none" class="numerico contratado">{{ concepto.EsActividad == '1' ? parseFloat(concepto.CantidadSubcontratada).formatMoney(2) : '' }}</td>
						<td style="display: none" class="numerico contratado">{{ concepto.EsActividad == '1' ? parseFloat(concepto.PrecioUnitario).formatMoney(2) : '' }}</td>
						<td style="display: none" class="numerico avance-volumen"></td>
						<td style="display: none" class="numerico avance-volumen">{{ concepto.EsActividad == '1' ? parseFloat(concepto.CantidadEstimadaTotal).formatMoney(2) : '' }}</td>
						<td style="display: none" class="numerico avance-volumen">{{ concepto.EsActividad == '1' ? parseFloat(concepto.PctAvance).formatMoney(2) : '' }}</td>
						<td style="display: none" class="numerico avance-importe"></td>
						<td style="display: none" class="numerico avance-importe">{{ concepto.EsActividad == '1' ? parseFloat(concepto.MontoEstimadoTotal).formatMoney(2) : '' }}</td>
						<td style="display: none" class="numerico saldo">{{ concepto.EsActividad == '1' ? concepto.CantidadSaldo : '' }}</td>
						<td style="display: none" class="numerico saldo">{{ concepto.EsActividad == '1' ? parseFloat(concepto.MontoSaldo).formatMoney(2) : '' }}</td>
						<td class="editable-cell numerico">
							<input v-on:change="changeCantidad(concepto)" class="text" v-if="concepto.EsActividad == '1'" v-model="concepto.CantidadEstimada"
								   :name="'CantidadEstimada' + i"
								   v-validate="{max_value: parseFloat(concepto.CantidadSaldo), min_value: 0}"
								   :class="{'is-invalid': errors.has('CantidadEstimada' + i)}"
							>
							<p v-else></p></td>
						<td class="editable-cell numerico">
							<input v-on:change="changePorcentaje(concepto)" class="text" v-if="concepto.EsActividad == '1'" v-model="concepto.PctEstimado"

							>
							<p v-else>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
						<td class="numerico">{{ concepto.EsActividad == '1' ? parseFloat(concepto.PrecioUnitario).formatMoney(2) : '' }}</td>
						<td class="editable-cell numerico">
							<input v-on:change="changeImporte(concepto)" class="text" v-if="concepto.EsActividad == '1'" v-model="concepto.ImporteEstimado"

							>
							<p v-else></p></td>
						<td style="display: none" class="destino" :title="concepto.RutaDestino">{{ concepto.RutaDestino }}</td>
					</tr>
					</tbody>
				</table>
			</div>
        </div>

		 <div class="row">
			<div class="col-md-12">
				<button class="btn btn-info pull-right" type="submit" @click="validate"
						:disabled="errors.count() > 0">
					Guardar
				</button>
			</div>
		</div>
	</span>
</template>

<script>
    import SubcontratoSelect from "../../cadeco/subcontrato/Select";
    export default {
        name: "estimacion-create",
        components: {SubcontratoSelect},
        data() {
            return {
                id_subcontrato: '',
                subcontrato: null,
                conceptos: null,
                cargando: false,
				columnas: [],
				fecha_inicio: '',
				fecha_fin: '',
				observaciones: '',
                fecha: ''
            }
        },

		mounted() {
        	this.fecha_inicio = new Date().toDate()
        	this.fecha_fin = new Date().toDate()
        	this.fecha = new Date().toDate()
        },

		methods: {
        	changeCantidad(concepto) {
				concepto.PctEstimado = ((concepto.CantidadEstimada / concepto.CantidadSubcontratada) * 100);
				concepto.ImporteEstimado = (concepto.CantidadEstimada * concepto.PrecioUnitario);
			},

			changePorcentaje(concepto) {
				concepto.CantidadEstimada = ((concepto.CantidadSubcontratada * concepto.PctEstimado) / 100);
				concepto.ImporteEstimado = (concepto.CantidadEstimada * concepto.PrecioUnitario);
			},

			changeImporte(concepto) {
				concepto.CantidadEstimada = (concepto.ImporteEstimado / concepto.PrecioUnitario);
				concepto.PctEstimado = ((concepto.CantidadEstimada / concepto.CantidadSubcontratada) * 100);
			},

			validate() {
				this.$validator.validate().then(result => {
					if (result) {
						this.store()
					}
				});
			},

			store() {
        		var conceptos = this.getConceptos();
        		if(conceptos.length > 0) {
					return this.$store.dispatch('contratos/estimacion/store', {
						id_antecedente: this.id_subcontrato,
                        fecha: this.fecha,
						cumplimiento: this.fecha_inicio,
						vencimiento: this.fecha_fin,
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
        		this.conceptos.forEach(concepto => {
        			if(parseFloat(concepto.CantidadEstimada) > 0) {
						conceptos.push({
							item_antecedente: concepto.IDConceptoContrato,
							id_concepto: concepto.IDConceptoDestino,
							importe: concepto.ImporteEstimado,
							cantidad: concepto.CantidadEstimada
						})
					}
				})
				return conceptos;
			}
		},

        watch: {
            id_subcontrato(id) {
            	if(id) {
					this.cargando = true;
					this.subcontrato = null;
					this.conceptos = null;

					this.$store.dispatch('contratos/subcontrato/find', {
						id: id,
						params: {
							include: 'empresa'
						}
					})
							.then(data => {
								this.$store.dispatch('contratos/subcontrato/getConceptosNuevaEstimacion', {
									id: id
								})
										.then(data => {
											this.conceptos = data;
										})
										.finally(() => {
											this.cargando = false;
										})
								this.subcontrato = data;
								this.observaciones = data.Observaciones;
							})
							.finally(() => {
								this.cargando = false;
							})
				} else {
            		this.subcontrato = null;
            		this.conceptos = null;
            		this.columnas = [];
            		this.observaciones = '';
				}
            },
			columnas(val) {
            	$('.contratado').css('display', 'none');
				$('.avance-volumen').css('display', 'none');
				$('.avance-importe').css('display', 'none');
				$('.saldo').css('display', 'none');
				$('.destino').css('display', 'none');

            	val.forEach(v => {
            		$('.' + v).removeAttr('style')
				})
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
</style>