<template>
    <span>
        <div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<label>
							Seleccione el subcontrato al que aplicará el convenio modificatorio:
						</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<model-list-select
								:disabled="cargando"
								name="id_subcontrato"
								v-model="id_subcontrato"
								option-value="id"
								:custom-text="numeroFolioAndRefernciaAndEmpresa"
								:list="subcontratos"
								:placeholder="!cargando?'Seleccionar o buscar por número de folio o referencia de subcontrato o razón social de contratista':'Cargando...'"
								:isError="errors.has(`id_subcontrato`)">
						</model-list-select>
					</div>
				</div>
			</div>
		</div>
        <div class="row" v-if="conceptos">

            <div class="col-md-1">
                <label for="fecha" class="col-form-label">Fecha: </label>
                <datepicker v-model = "fecha"
                            id="fecha"
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
            <div class="col-md-3">
                <label for="convenio" class="col-form-label">Convenio (PDF): </label>
                <input type="file" class="form-control" id="convenio"
                       @change="onFileChange"
                       row="3"
                       v-validate="{ ext: ['pdf']}"
                       name="convenio_pdf"
                       data-vv-as="Convenio"
                       ref="convenio_pdf"
                       :class="{'is-invalid': errors.has('convenio_pdf')}">
                <div class="invalid-feedback" v-show="errors.has('convenio_pdf')">{{ errors.first('convenio_pdf') }} (pdf)</div>
            </div>

        </div>
        <br />
		<div class="row">
			<div class="col-md-6">
				<div class="card" v-if="conceptos">
					<div class="card-header">
						<h6 class="card-title">Subcontrato</h6>
					</div>
					<div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Fecha:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.fecha_format }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Referencia:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.referencia }}
                                    </div>
                                </div>
                                <div class="form-group row" v-if="subcontrato.empresa">
                                    <label class="col-md-3 col-form-label">Contratista:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.empresa.razon_social }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Moneda:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.moneda }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">IVA:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.impuesto_format }}
                                    </div>
                                </div>
                                <div class="form-group row" v-if="subcontrato.empresa">
                                    <label class="col-md-3 col-form-label">Monto:</label>
                                    <div class="col-md-9">
                                        {{ subcontrato.monto_format }}
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="card" v-if="conceptos">
					<div class="card-header">
						<h6 class="card-title">Valor de los cambios</h6>
					</div>
					<div class="card-body">
						<form>
							<div class="form-row">
                                <label class="col-md-3 col-form-label">Monto:</label>
                                <div class="col-md-9">
                                    $ {{  parseFloat(importe_addendum).formatMoney(2)  }}
                                </div>

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="card" v-if="conceptos" style="display:none">
			<div class="card-body">
				<div class="form-check form-check-inline">
					<input v-model="columnas" class="form-check-input" type="checkbox" value="contratado" id="contratado" >
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

        <div class="row" v-if="conceptos">
            <div class="col-md-12">
                 <button class="btn btn-primary pull-right" title="Agregar concepto extraordinario">
                     <i class="fa fa-plus-circle"></i> Agregar Concepto
                 </button>
            </div>
        </div>
        <br />
		<div class="card" v-if="conceptos">
			<div class="card-body table-responsive">
				<table id="tabla-conceptos">
					<thead>
						<tr>
							<th rowspan="2">Clave</th>
							<th rowspan="2">Concepto</th>
							<th rowspan="2">UM</th>
							<th colspan="2" class="contratado">Contratado</th>
							<th colspan="2" class="avance-volumen">Avance</th>

							<th colspan="2" class="saldo">Saldo</th>
							<th colspan="3">Addendum</th>
							<th class="destino">Distribución</th>
						</tr>
						<tr>
							<th class="contratado">Volumen</th>
							<th class="contratado">P.U.</th>
							<th class="avance-volumen">Volumen</th>
							<th class="avance-importe">Importe</th>
							<th class="saldo">Volumen</th>
							<th class="saldo">Importe</th>
							<th style="width: 80px">Volumen</th>
							<th>P.U.</th>
							<th>Importe</th>
							<th class="destino">Destino</th>
						</tr>
					</thead>
					<tbody v-for="(concepto, i) in conceptos">
                        <tr v-if="!concepto.unidad">
                            <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                            <td :title="concepto.descripcion">
                                <span v-for="n in concepto.nivel">&nbsp;</span>
                                <b>{{concepto.descripcion}}</b></td>
                            <td></td>
                            <td class="numerico contratado"/>
                            <td class="numerico contratado"/>
                            <td class="numerico avance-volumen"/>
                            <td class="numerico avance-importe"/>
                            <td class="numerico saldo"/>
                            <td class="numerico saldo"/>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="destino"/>
                        </tr>
					    <tr v-else>
						    <td :title="concepto.clave">{{ concepto.clave }}</td>
                            <td :title="concepto.descripcion_concepto">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                {{concepto.descripcion_concepto}}
                            </td>
                            <td class="centrado">{{concepto.unidad}}</td>
                            <td class="numerico contratado">{{ parseFloat(concepto.cantidad_subcontrato).formatMoney(2) }}</td>
                            <td class="numerico contratado">{{ parseFloat(concepto.precio_unitario_subcontrato).formatMoney(2) }}</td>
                            <td class="numerico avance-volumen">{{ parseFloat(concepto.cantidad_estimada_anterior).formatMoney(2) }}</td>
                            <td class="numerico avance-importe">{{ parseFloat(concepto.importe_estimado_anterior).formatMoney(4) }}</td>
                            <td class="numerico saldo">{{  parseFloat(concepto.cantidad_por_estimar).formatMoney(2) }}</td>
                            <td class="numerico saldo">{{ parseFloat(concepto.importe_por_estimar).formatMoney(4) }}</td>
                            <td class="editable-cell numerico" style="background-color: #ddd">
                                <input v-on:keyup="keyupCantidad(concepto)"
                                       v-on:change="changeCantidad()"
                                       class="text"
                                       v-model="concepto.cantidad_addendum"
                                       :name="`cantidadEstimacion[${concepto.id}]`"
                                       v-validate="{min_value: parseFloat((concepto.cantidad_por_estimar*-1)).toFixed(2)}"
                                       :class="{'is-invalid': errors.has(`cantidadEstimacion[${concepto.id}]`)}" />
                                 <div class="invalid-feedback" v-show="errors.has(`cantidadEstimacion[${concepto.id}]`)">{{ errors.first(`cantidadEstimacion[${concepto.id}]`) }}</div>
                            </td>
                            <td class="numerico" style="background-color: #ddd; text-decoration: underline">{{ concepto.precio_unitario_subcontrato_format}}</td>
                            <td class="numerico" style="background-color: #ddd">
                                {{ parseFloat(concepto.importe_addendum).formatMoney(4) }}
                            </td>
                            <td  class="destino" :title="concepto.destino_path_larga">{{ concepto.destino_path }}</td>
                        </tr>
                    </tbody>
				</table>
			</div>
        </div>

        <div class="form-group row" v-if="conceptos">
            <label class="col-md-1 col-form-label">Observaciones:</label>
            <div class="col-md-11">
                <textarea
                    name="observaciones"
                    id="observaciones"
                    class="form-control"
                    v-model="observaciones"
                ></textarea>
            </div>
        </div>

		 <div class="row">
			<div class="col-md-12">
				<button class="btn btn-primary float-right" type="submit" @click="validate"
						:disabled="errors.count() > 0">
                    <i class="fa fa-save"></i>
					Guardar
				</button>
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
				subcontratos: [],
                fechasDeshabilitadas:{},
                fecha_hoy : '',
                importe_addendum:0,
            }
        },

		mounted() {
        	this.fecha_inicio = new Date()
        	this.fecha_fin = new Date()
        	this.fecha = new Date()
            this.fecha_hoy = new Date()
            this.fechasDeshabilitadas.from= new Date();
			this.getSubcontratos()
        },

		methods: {
			numeroFolioAndRefernciaAndEmpresa(item){
				return `[${item.numero_folio_format}] - [${item.referencia}]- [${item.empresa}]`
			},
            formatoFecha(date){
                return moment(date).format('DD/MM/YYYY');
            },
            changeCantidad() {
                var suma;
                suma=0;
                this.conceptos.forEach(function(concepto) {
                    if(!isNaN(concepto.importe_addendum)){
                        suma +=  Number(concepto.cantidad_addendum )*Number(concepto.precio_unitario_subcontrato);
                    }


                });
                this.importe_addendum = suma;
            },
            keyupCantidad(concepto)
            {
                concepto.importe_addendum = (concepto.cantidad_addendum * concepto.precio_unitario_subcontrato).toFixed(2);
            },
			validate() {
				this.$validator.validate().then(result => {
					if (result) {
                        this.store();
					}
				});
			},

			store() {
        		var conceptos = this.getConceptos();
        		if(conceptos.length > 0) {
					return this.$store.dispatch('contratos/estimacion/store', {
						id_antecedente: this.id_subcontrato,
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
        		    swal('','Debe modificar o agregar al menos un concepto','warning');
				}
			},

			getSubcontratos() {
				this.subcontratos = [];
				this.cargando = true;
				return this.$store.dispatch('contratos/subcontrato/index', {
					params: {
						scope: 'estimable',
						sort: 'id',
						order: 'desc'
					}
				})
						.then(data => {
							this.subcontratos = data;
							this.cargando = false;
						})
			},

			getConceptos() {
        		var conceptos = [];
                for (var key in this.conceptos) {
                    var obj = this.conceptos[key];
                    if( typeof obj.para_estimar === 'undefined') {
                        if (parseFloat(obj.cantidad_addendum) !== 0) {
                            conceptos.push({
                                item_antecedente: obj.id_concepto,
                                id_concepto: obj.id_destino,
                                importe: obj.importe_addendum,
                                cantidad: obj.cantidad_addendum
                            })
                        }
                    }
                }
				return conceptos;
			},
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file = e.target.result;
                };
                reader.readAsDataURL(file);

            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.nombre = files[0].name;
                if(e.target.id == 'convenio_pdf') {
                    this.createImage(files[0]);
                }
            },
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
							include: ['empresa','partidas_convenio']
						}
					})
							.then(data => {
                                this.conceptos = data.partidas_convenio.data;
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
		background-color: #ddd;
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
