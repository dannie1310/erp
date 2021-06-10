<template>
    <span>
        <div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<label>
							Seleccione el subcontrato al que le desea generar una solicitud de cambio:
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
        <div class="card" v-if="!conceptos && id_subcontrato">
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
        <div class="card" v-if="conceptos" >
			<div class="card-body">
                <div class="row" >
                    <div class="col-md-2">
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
                        <label for="archivo" class="col-form-label">Soporte (PDF): </label>
                        <input type="file" class="form-control" id="archivo"
                               @change="onFileChange"
                               row="3"
                               v-validate="{ ext: ['pdf']}"
                               name="archivo"
                               data-vv-as="Soporte"
                               ref="archivo"
                               :class="{'is-invalid': errors.has('archivo')}">
                        <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (pdf)</div>
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
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td>
                                                    <b>Fecha:</b>
                                                </td>
                                                <td>
                                                    {{ subcontrato.fecha_format }}
                                                </td>
                                                 <td>
                                                    <b>Folio:</b>
                                                </td>
                                                <td>
                                                   {{subcontrato.numero_folio_format}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                     <b>Contratista:</b>
                                                </td>
                                                <td colspan="3">
                                                     {{ subcontrato.empresa.razon_social }}
                                                </td>
                                            </tr>
                                            <tr v-if="subcontrato.referencia != ''">
                                                <td>
                                                     <b>Referencia:</b>
                                                </td>
                                                <td colspan="3">
                                                     {{ subcontrato.referencia }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Moneda:</b>
                                                </td>
                                                <td>
                                                    {{ subcontrato.moneda }}

                                                </td>
                                                 <td>
                                                    <b>Total:</b>
                                                </td>
                                                <td>
                                                   {{ subcontrato.monto_format }}
                                                </td>
                                            </tr>

                                        </table>
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td>
                                                        <b>Subtotal:</b>
                                                    </td>
                                                    <td>
                                                        ${{ parseFloat(importe_addendum).formatMoney(2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <b>IVA:</b>
                                                    </td>
                                                    <td>
                                                        ${{ parseFloat(impuesto_addendum).formatMoney(2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <b>Total:</b>
                                                    </td>
                                                    <td>
                                                        ${{ parseFloat(monto_addendum).formatMoney(2) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <b>Porcentaje:</b>
                                                    </td>
                                                    <td>
                                                        {{ parseFloat(porcentaje_addendum).formatMoney(4) }}%
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" >
                    <div class="col-md-12">
                        <div class="pull-right">
                            <CreateConceptosExtaordinarios
                                v-on:agrega-extraordinarios="onAgregaExtraordinarios"
                                v-bind:id_contrato_proyectado="subcontrato.id_contrato_proyectado"
                                v-bind:tiene_nodo_extraordinario="subcontrato.tiene_nodo_extraordinario">
                            </CreateConceptosExtaordinarios>
                            <ConceptoExtraordinario
                                v-on:agrega-extraordinario="onAgregaExtraordinario"
                                v-bind:concepto="concepto_extraordinario">
                            </ConceptoExtraordinario>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="tabla-conceptos" >
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
                                    <th style="width: 20px"></th>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(concepto, i) in conceptos">
                                    <tr v-if="!concepto.unidad">
                                        <td :title="concepto.clave"><b>{{concepto.clave}}</b></td>
                                        <td :title="concepto.descripcion">
                                            <span v-for="n in concepto.nivel">&nbsp;</span>
                                            <b>{{concepto.descripcion}}</b>
                                        </td>
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
                                        <td></td>
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
                                        <td class="editable-cell numerico" style="background-color: #ddd" v-if="concepto.precio_modificado ==0">
                                            <input v-on:keyup="keyupCantidad(concepto)"
                                                   v-on:change="changeCantidad()"
                                                   class="text"
                                                   v-model="concepto.cantidad_addendum"
                                                   :name="`cantidadsolicitud_cambio[${concepto.id}]`"
                                                   v-validate="{min_value: parseFloat((concepto.cantidad_por_estimar*-1)).toFixed(2)}"
                                                   :class="{'is-invalid': errors.has(`cantidadsolicitud_cambio[${concepto.id}]`)}" />
                                             <div class="invalid-feedback" v-show="errors.has(`cantidadsolicitud_cambio[${concepto.id}]`)">{{ errors.first(`cantidadsolicitud_cambio[${concepto.id}]`) }}</div>
                                        </td>
                                        <td class="numerico" style="background-color: #ddd;"
                                            v-else>
                                            {{ parseFloat((concepto.cantidad_addendum)).toFixed(2) }}
                                        </td>
                                        <td class="numerico" style="background-color: #ddd; text-decoration: underline; cursor:pointer"
                                            v-on:dblclick="onCambioPrecio(concepto)" v-if="concepto.precio_modificado ==0">
                                            {{ concepto.precio_unitario_subcontrato_format}}
                                        </td>
                                        <td class="numerico" style="background-color: #ddd;"
                                            v-else>
                                            {{ concepto.precio_unitario_subcontrato_format}}
                                        </td>
                                        <td class="numerico" style="background-color: #ddd">
                                            ${{ parseFloat(concepto.importe_addendum).formatMoney(4) }}
                                        </td>
                                        <td  class="destino" :title="concepto.destino_path_larga">{{ concepto.destino_path }}</td>
                                        <td></td>
                                    </tr>
                                </template>
                                <tr v-if="conceptos_extraordinarios.length>0">
                                    <td></td>
                                    <td colspan="13"><b>&nbsp;&nbsp;Nuevos Conceptos Extraordinarios</b></td>
                                </tr>

                                <template v-for="(concepto_extraordinario, j) in conceptos_extraordinarios">

                                    <tr v-if="concepto_extraordinario.cantidad_hijos == 0" :style="!concepto_extraordinario.destino > 0 || concepto_extraordinario.unidad == '' || concepto_extraordinario.clave == ''?`background-color : #f5c6cb`:``">
                                        <td :title="concepto_extraordinario.clave" v-if="concepto_extraordinario.clave !==''">{{concepto_extraordinario.clave}}</td>
                                        <td :title="concepto_extraordinario.clave" v-else style="background-color: #e75757">{{concepto_extraordinario.clave_error}}</td>
                                        <td :title="concepto_extraordinario.descripcion">
                                            <span v-for="n in concepto_extraordinario.nivel">&nbsp;</span>
                                            {{concepto_extraordinario.descripcion}}
                                        </td>
                                        <td class="centrado" v-if="concepto_extraordinario.unidad !==''">{{concepto_extraordinario.unidad}}</td>
                                        <td class="centrado" v-else style="background-color: #e75757">{{concepto_extraordinario.unidad_error}}</td>
                                        <td class="numerico contratado"></td>
                                        <td class="numerico contratado"></td>
                                        <td class="numerico avance-volumen"></td>
                                        <td class="numerico avance-importe"></td>
                                        <td class="numerico saldo"></td>
                                        <td class="numerico saldo"></td>
                                        <td class="numerico saldo" style="background-color: #ddd">
                                            {{ parseFloat(concepto_extraordinario.cantidad).formatMoney(2) }}
                                        </td>
                                        <td class="numerico" style="background-color: #ddd">
                                            ${{ parseFloat(concepto_extraordinario.precio).formatMoney(2)  }}
                                        </td>
                                        <td class="numerico" style="background-color: #ddd">
                                            ${{ parseFloat(concepto_extraordinario.importe).formatMoney(2)  }}

                                        </td>
                                        <td  class="destino" :title="concepto_extraordinario.destino_path" v-if="concepto_extraordinario.destino>0">{{ concepto_extraordinario.destino_path_corta }}</td>
                                        <td  class="destino" v-else style="background-color: #e75757">{{ concepto_extraordinario.destino_error }}</td>
                                        <td>
                                            <button @click="eliminarPartida(j)" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-trash" v-else></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-else :style="concepto_extraordinario.clave == ''?`background-color : #f5c6cb`:``">
                                        <td :title="concepto_extraordinario.clave" v-if="concepto_extraordinario.clave !==''"><b>{{concepto_extraordinario.clave}}</b></td>
                                        <td :title="concepto_extraordinario.clave" v-else style="background-color: #e75757"><b>{{concepto_extraordinario.clave_error}}</b></td>
                                        <td >
                                            <span v-for="n in concepto_extraordinario.nivel">&nbsp;</span>
                                            <b>{{concepto_extraordinario.descripcion}}</b>
                                        </td>
                                        <td class="centrado"></td>
                                        <td class="numerico contratado"></td>
                                        <td class="numerico contratado"></td>
                                        <td class="numerico avance-volumen"></td>
                                        <td class="numerico avance-importe"></td>
                                        <td class="numerico saldo"></td>
                                        <td class="numerico saldo"></td>
                                        <td class="numerico saldo" >
                                        </td>
                                        <td class="numerico" ></td>
                                        <td class="numerico" ></td>
                                        <td  class="destino" ></td>
                                        <td>
                                            <button @click="eliminarPartida(j)" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-trash" v-else></i>
                                            </button>
                                        </td>
                                    </tr>

                                </template>

                                <tr v-if="conceptos_cambios_precio.length>0">
                                    <td></td>
                                    <td colspan="13"><b>&nbsp;&nbsp;Nuevos Conceptos Con Cambio de Precio</b></td>
                                </tr>
                                <tr  v-for="(concepto_cambio_precio, k) in conceptos_cambios_precio" :key="concepto_cambio_precio.id">
                                    <td >{{ concepto_cambio_precio.clave }}</td>
                                    <td >
                                        {{concepto_cambio_precio.descripcion}}
                                    </td>
                                    <td class="centrado">{{concepto_cambio_precio.unidad}}</td>
                                    <td class="numerico contratado"></td>
                                    <td class="numerico contratado"></td>
                                    <td class="numerico avance-volumen"></td>
                                    <td class="numerico avance-importe"></td>
                                    <td class="numerico saldo"></td>
                                    <td class="numerico saldo"></td>
                                    <td class="numerico saldo" style="background-color: #ddd">
                                        {{ parseFloat(concepto_cambio_precio.cantidad).formatMoney(2) }}
                                    </td>
                                    <td class="editable-cell numerico" style="background-color: #ddd">
                                        ${{ parseFloat(concepto_cambio_precio.precio).formatMoney(2)  }}
                                    </td>
                                    <td class="numerico" style="background-color: #ddd">
                                        ${{ parseFloat(concepto_cambio_precio.importe).formatMoney(2)  }}
                                    </td>
                                    <td  class="destino" :title="concepto_cambio_precio.destino_path">{{ concepto_cambio_precio.destino_path_corta }}</td>
                                    <td>
                                        <button @click="eliminarPartidaCambioPrecio(k,concepto_cambio_precio)" type="button" class="btn btn-sm btn-outline-danger pull-left" title="Eliminar">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-trash" v-else></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table >
                    </div>
                </div>

    <br />

   <div class="row">
       <div class="col-md-12"><b>Observaciones:</b> </div>
   </div>
   <div class="row">
       <div class="col-md-12">
           <textarea
               name="observaciones"
               id="observaciones"
               class="form-control"
               v-model="observaciones"
           ></textarea>
       </div>
   </div>
    <br />

        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary float-right" type="submit" @click="validate"
                        :disabled="errors.count() > 0 || !conceptos">
                    <i class="fa fa-save"></i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>

        <div class="modal fade" ref="modalCambioPrecio" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-dollar-sign"></i>Cambio de Precio a Concepto</h5>
                        <button type="button" class="close" @click="cerrarCambioPrecio()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <div class="form-group row error-content">
                                <label  class="col-md-3 col-form-label">Clave:</label>
                                <div class="col-md-4">
                                    {{concepto_cambio_precio.clave}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Descripción:</label>
                                <div class="col-md-9">
                                    {{concepto_cambio_precio.descripcion}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Cantidad:</label>
                                <div class="col-md-3">
                                    {{ parseFloat(concepto_cambio_precio.cantidad).formatMoney(2)  }}
                                </div>
                                <label class="col-md-3 col-form-label">Unidad:</label>
                                <div class="col-md-3">
                                    {{concepto_cambio_precio.unidad}}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Precio:</label>
                                <div class="col-md-4">
                                    $ {{ parseFloat(concepto_cambio_precio.precio).formatMoney(2)  }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="precio" class="col-md-3 col-form-label">Nuevo Precio:</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="precio" placeholder="Precio"
                                           v-model="concepto_cambio_precio.precio_nuevo"
                                           name="precio"
                                    >
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarCambioPrecio()">
                                <i class="fa fa-close"  ></i>
                                Cerrar
                            </button>
                            <button type="button" class="btn btn-primary" @click="validateCambioPrecio()">
                                <i class="fa fa-plus-circle"></i>
                                Agregar
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
    import ConceptoExtraordinario from './partials/CreateConceptoExtaordinario';
    import CreateConceptosExtaordinarios from "./partials/CreateConceptosExtaordinarios";
    let id_cambio_precio  = 0;
    export default {
        name: "solicitud_cambio-create",
        components: {CreateConceptosExtaordinarios, ModelListSelect, Datepicker, es, ConceptoExtraordinario},
        data() {
            return {
                archivo:'',
                archivo_nombre:'',
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
                tasa_iva : '',
                importe_addendum:0,
                monto_addendum:0,
                impuesto_addendum:0,
                porcentaje_addendum:0,
                conceptos_extraordinarios : [],
                concepto_extraordinario :{
                    clave:'',
                    descripcion:'',
                    cantidad:'',
                    unidad:'',
                    destino:'',
                    precio:'',
                    nivel:1,
                    es_hoja:0,
                    cantidad_hijos:0,
                    id_nodo_carga:'',
                },
                conceptos_cambios_precio : [],
                concepto_cambio_precio :{
                    clave:'',
                    descripcion:'',
                    cantidad:'',
                    unidad:'',
                    destino:'',
                    precio:'',
                    importe:'',
                    precio_nuevo:'',
                    id_item_subcontrato:'',
                },
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
                this.conceptos_extraordinarios.forEach(function(concepto_extraordinario) {
                    if(!isNaN(concepto_extraordinario.importe)){
                        suma +=  Number(concepto_extraordinario.cantidad )*Number(concepto_extraordinario.precio);
                    }
                });
                this.conceptos_cambios_precio.forEach(function(concepto_cambio_precio) {
                    if(!isNaN(concepto_cambio_precio.importe)){
                        suma +=  Number(concepto_cambio_precio.cantidad )*Number(concepto_cambio_precio.precio);
                    }
                });
                this.importe_addendum = suma;
                this.monto_addendum = suma * (1+(Number(this.tasa_iva)/100));
                this.impuesto_addendum = suma * Number(this.tasa_iva)/100;
                this.porcentaje_addendum = this.monto_addendum / this.subcontrato.monto * 100 ;
            },
            keyupCantidad(concepto)
            {
                concepto.importe_addendum = (concepto.cantidad_addendum * concepto.precio_unitario_subcontrato).toFixed(2);
            },
            keyupPrecio(concepto)
            {
                concepto.importe = (concepto.cantidad * concepto.precio).toFixed(2);
            },
			validate() {
				this.$validator.validate().then(result => {
				    let error_destinos = false;
                    let error_unidades = false;
                    let error_claves = false;
					if (result) {
					    this.conceptos_extraordinarios.forEach(function(concepto_extraordinario, i){
					        if(!concepto_extraordinario["destino"]>0 && concepto_extraordinario.es_hoja == true)
                            {
                                error_destinos = true;
                            }

                            if(concepto_extraordinario["unidad"]==='' && concepto_extraordinario.es_hoja == true)
                            {
                                error_unidades = true;
                            }

                            if(concepto_extraordinario["clave"]==='')
                            {
                                error_claves = true;
                            }
                        });
					    if(error_destinos === false && error_unidades == false && error_claves == false){
                            this.store();
                        } else if(error_destinos === false && error_unidades == false && error_claves == true){
                            swal('Error',"Hay partidas extraordinarias con errores en la clave, favor de corregir para poder realizar el registro",'error');
                        } else if(error_destinos === false && error_unidades == true && error_claves == false){
                            swal('Error',"Hay partidas extraordinarias con errores en la unidad, favor de corregir para poder realizar el registro",'error');
                        } else if(error_destinos === false && error_unidades == true && error_claves == true){
                            swal('Error',"Hay partidas extraordinarias con errores en la clave y unidad, favor de corregir para poder realizar el registro",'error');
                        } else if(error_destinos === true && error_unidades == false && error_claves == false){
					        swal('Error',"Hay partidas extraordinarias con errores en el destino, favor de corregir para poder realizar el registro",'error');
                        }  else if(error_destinos === true && error_unidades == false && error_claves == true){
                            swal('Error',"Hay partidas extraordinarias con errores en la clave y el destino, favor de corregir para poder realizar el registro",'error');
                        } else if(error_destinos === true && error_unidades == true && error_claves == false){
                            swal('Error',"Hay partidas extraordinarias con errores en la unidad y el destino, favor de corregir para poder realizar el registro",'error');
                        } else {
                            swal('Error',"Hay partidas extraordinarias con errores en la clave, unidad y destino, favor de corregir para poder realizar el registro",'error');
                        }

					}
				});
			},
            validateCambioPrecio(){

                if(isNaN(this.concepto_cambio_precio.precio_nuevo)){
                    swal('','Debe ingresar un precio válido','error');
                } else {
                    if(!this.concepto_cambio_precio.precio_nuevo>0){
                        swal('','Debe ingresar un precio mayor a 0','error');
                    } else{
                        this.onAgregaCambioPrecio();
                        this.cerrarCambioPrecio();
                    }
                }
            },
			store() {
        		var conceptos = this.getConceptos();
                var conceptos_cambio_precio = this.getConceptosCambioPrecio();
        		if(conceptos.length > 0 || this.conceptos_extraordinarios.length > 0) {
					return this.$store.dispatch('contratos/solicitud-cambio/store', {
						id_subcontrato: this.id_subcontrato,
                        monto: this.monto_addendum,
                        impuesto: this.impuesto_addendum,
                        fecha: this.fecha,
						observaciones: this.observaciones,
                        conceptos_cambios_precio: conceptos_cambio_precio,
                        conceptos_extraordinarios: this.conceptos_extraordinarios,
                        conceptos: conceptos,
                        archivo: this.archivo,
                        archivo_nombre: this.archivo_nombre
					})
                    .then(data=> {
                        this.$router.push({name: 'solicitud-cambio'});
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
						sort: 'id_transaccion',
						order: 'desc'
					}
				})
                .then(data => {
                    this.subcontratos = data;
                    this.cargando = false;
                })
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
            onCambioPrecio(concepto){
			    if(concepto.cantidad_por_estimar>0){
                    this.concepto_cambio_precio ={
                        clave:concepto.clave,
                        descripcion: concepto.descripcion_concepto,
                        cantidad: concepto.cantidad_por_estimar,
                        unidad:concepto.unidad,
                        precio:concepto.precio_unitario_subcontrato,
                        precio_nuevo:'',
                        importe:concepto.importe_subcontrato,
                        concepto:concepto,
                        id_item_subcontrato:concepto.id
                    };

                    $(this.$refs.modalCambioPrecio).modal('show');
                } else {
                    swal('Atención','No se puede modificar el precio si la partida se ha estimado completamente','warning');
                }
            },
            onAgregaCambioPrecio()
            {
                this.concepto_cambio_precio.concepto.cantidad_addendum = parseFloat(this.concepto_cambio_precio.concepto.cantidad_por_estimar * -1).toFixed(2);
                this.concepto_cambio_precio.concepto.importe_addendum = (this.concepto_cambio_precio.concepto.cantidad_addendum * this.concepto_cambio_precio.concepto.precio_unitario_subcontrato).toFixed(2);
                this.concepto_cambio_precio.concepto.precio_modificado = 1;
                this.conceptos_cambios_precio.push({
                    clave:this.concepto_cambio_precio.concepto.clave,
                    descripcion:this.concepto_cambio_precio.concepto.descripcion_concepto,
                    unidad:this.concepto_cambio_precio.concepto.unidad,
                    cantidad:this.concepto_cambio_precio.concepto.cantidad_por_estimar,
                    destino:this.concepto_cambio_precio.concepto.destino,
                    destino_path:this.concepto_cambio_precio.concepto.destino_path_larga,
                    destino_path_corta:this.concepto_cambio_precio.concepto.destino_path,
                    precio:this.concepto_cambio_precio.precio_nuevo,
                    importe:(this.concepto_cambio_precio.precio_nuevo * this.concepto_cambio_precio.concepto.cantidad_por_estimar),
                    concepto:this.concepto_cambio_precio.concepto,
                    id_item_subcontrato: this.concepto_cambio_precio.id_item_subcontrato,
                });
                this.changeCantidad();
            },
            cerrarCambioPrecio(){
                $(this.$refs.modalCambioPrecio).modal('hide');
            },
            onAgregaExtraordinario(concepto){
                return this.$store.dispatch('cadeco/concepto/find', {
                    id: concepto.destino,
                    params: {
                    }
                })
                    .then(data => {
                    this.conceptos_extraordinarios.push({
                        clave:concepto.clave,
                        descripcion:concepto.descripcion,
                        unidad:concepto.unidad,
                        cantidad:concepto.cantidad,
                        destino:concepto.destino,
                        destino_path:data.path,
                        destino_path_corta:data.path_corta,
                        precio:concepto.precio,
                        nivel:1,
                        es_hoja:0,
                        cantidad_hijos:0,
                        id_nodo_carga:'',
                        importe:(concepto.cantidad * concepto.precio).toFixed(2),
                    });

                    this.concepto_extraordinario ={
                        clave:'',
                        descripcion:'',
                        cantidad:'',
                        unidad:'',
                        destino:'',
                        destino_path:'',
                        destino_path_corta:'',
                        precio:'',
                        importe:'',
                        nivel:1,
                        es_hoja:0,
                        cantidad_hijos:0,
                        id_nodo_carga:'',
                    };
                    this.changeCantidad();
                })
            },
            onAgregaExtraordinarios(partidas){
			    this.conceptos_extraordinarios = partidas;
                this.changeCantidad();
            },
            eliminarPartida(index){
                this.conceptos_extraordinarios.splice(index, 1);
                this.changeCantidad();
            },
            eliminarPartidaCambioPrecio(index, concepto_cambio_precio){
                concepto_cambio_precio.concepto.cantidad_addendum = '';
                concepto_cambio_precio.concepto.importe_addendum = 0;
                concepto_cambio_precio.concepto.precio_modificado = 0;
                this.conceptos_cambios_precio.splice(index, 1);
                this.changeCantidad();
            },
			getConceptos() {
        		var conceptos = [];
                for (var key in this.conceptos) {
                    var obj = this.conceptos[key];

                    if (!isNaN(parseFloat(obj.cantidad_addendum))) {
                        conceptos.push({
                            item_antecedente: obj.id_concepto,
                            id_concepto: obj.id_destino,
                            importe: obj.importe_addendum,
                            precio: obj.precio_unitario_subcontrato,
                            cantidad: obj.cantidad_addendum,
                            precio_modificado: obj.precio_modificado,
                            id_item_subcontrato: obj.id
                        })
                    }
                }
				return conceptos;
			},
            getConceptosCambioPrecio() {
                var conceptos_cambio_precio = [];
                for (var key in this.conceptos_cambios_precio) {
                    var obj = this.conceptos_cambios_precio[key];
                    if(obj.concepto != undefined){
                        conceptos_cambio_precio.push({
                            clave:obj.clave,
                            descripcion:obj.descripcion,
                            unidad:obj.unidad,
                            cantidad:obj.cantidad,
                            destino:obj.destino,
                            destino_path:obj.destino_path,
                            destino_path_corta:obj.destino_path_corta,
                            precio:obj.precio,
                            importe:obj.importe,
                            id_concepto_original:obj.concepto.id_concepto,
                            id_item_subcontrato:obj.id_item_subcontrato
                        })
                    }
                }
                return conceptos_cambio_precio;
            },
            createImage(file) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.archivo = e.target.result;
                };
                reader.readAsDataURL(file);

            },
            onFileChange(e){
                this.archivo = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.archivo_nombre = files[0].name;
                if(e.target.id == 'archivo') {
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
							include: ['empresa','partidas_convenio', 'obra']
						}
					})
							.then(data => {
                                this.conceptos = data.partidas_convenio.data;
								this.subcontrato = data;
								this.observaciones = data.Observaciones;
								this.tasa_iva = data.obra.iva;
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

    table#tabla-conceptos th, table#tabla-conceptos td {
        border: 1px solid #dee2e6;
    }

    table thead th
    {
        padding: 0.2em;

        background-color: #f2f4f5;
        font-weight: bold;
        color: black;
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
