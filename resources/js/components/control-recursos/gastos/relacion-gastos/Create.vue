<template>
    <span>
        <nav>
            <div class="row">
                 <div class="col-12">
                     <div class="invoice p-3 mb-3">
                         <form role="form" @submit.prevent="validate">
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-6">
                                        <div class="form-group row error-content">
                                            <label for="id_empresa">Empresa:</label>
                                            <select class="form-control"
                                                    data-vv-as="Empresa"
                                                    id="id_empresa"
                                                    name="id_empresa"
                                                    :error="errors.has('id_empresa')"
                                                    v-validate="{required: true}"
                                                    v-model="id_empresa">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(empresa) in empresas" :value="empresa.id">{{ empresa.razon_social }} - [ {{empresa.rfc}} ]</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="form-group row error-content">
                                            <label for="id_empleado">Empleado:</label>
                                            <select class="form-control"
                                                    data-vv-as="Empleado"
                                                    id="id_empleado"
                                                    name="id_empleado"
                                                    :error="errors.has('id_empleado')"
                                                    v-validate="{required: true}"
                                                    v-model="id_empleado">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(e) in empleados" :value="e.id">{{ e.nombre }}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empleado')">{{ errors.first('id_empleado') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-6">
                                        <div class="form-group row error-content">
                                            <h6><b>Departamento:</b></h6>
                                            <h6>{{departamento}}</h6>
                                        </div>
                                     </div>
                                     <div class="col-md-6">
                                        <div class="form-group row error-content">
                                            <label for="id_empleado">Proyecto:</label>
                                            <select class="form-control"
                                                    data-vv-as="Empresa"
                                                    id="id_empleado"
                                                    name="id_empleado"
                                                    :error="errors.has('id_empleado')"
                                                    v-validate="{required: true}"
                                                    v-model="id_empleado">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(empresa) in empresas" :value="empresa.id">{{ empresa.razon_social }} - [ {{empresa.rfc}} ]</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empleado')">{{ errors.first('id_empleado') }}</div>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-1">
                                         <div class="form-group error-content">
                                             <label class="col-form-label">Periodo:</label>
                                         </div>
                                     </div>
                                     <div class="col-md-2">
                                         <div class="form-group error-content">
                                             <datepicker v-model = "fecha_inicial"
                                                         name = "fecha_inicial"
                                                         :format = "formatoFecha"
                                                         data-vv-as="Fecha Inicial"
                                                         :language = "es"
                                                         :bootstrap-styling = "true"
                                                         class = "form-control"
                                                         v-validate="{required: true}"
                                                         :disabled-dates="fechasDeshabilitadas"
                                                         :class="{'is-invalid': errors.has('fecha_inicial')}"
                                             />
                                             <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                                         </div>
                                     </div>
                                     -->
                                     <div class="col-md-2">
                                         <div class="form-group error-content">
                                             <datepicker v-model = "fecha_final"
                                                         name = "fecha_final"
                                                         data-vv-as="Fecha Final"
                                                         :format = "formatoFecha"
                                                         :language = "es"
                                                         :bootstrap-styling = "true"
                                                         class = "form-control"
                                                         v-validate="{required: true}"
                                                         :disabled-dates="fechasDeshabilitadas"
                                                         :class="{'is-invalid': errors.has('fecha_final')}"
                                             />
                                             <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-6">
                                         <div class="form-group error-content">
                                             <label for="motivo">Motivo:</label>
                                             <input
                                                 name="motivo"
                                                 id="motivo"
                                                 class="form-control"
                                                 v-model="motivo"
                                                 v-validate="{required: true}"
                                                 data-vv-as="Motivo"
                                                 :class="{'is-invalid': errors.has('motivo')}" />
                                             <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                         <div class="form-group error-content float-right">
                                             <select class="form-control"
                                                    data-vv-as="Moneda"
                                                    id="id_moneda"
                                                    name="id_moneda"
                                                    :error="errors.has('id_moneda')"
                                                    v-validate="{required: true}"
                                                    v-model="id_moneda">
                                                <option value>-- Selecionar --</option>
                                                <option v-for="(m) in monedas" :value="m.id">{{m.moneda}}</option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <hr />
                                 <div class="row">
                                     <div class="col-md-12">
                                         <button type="button" class="btn btn-success btn-sm pull-right" @click="modalCFDI()" v-if="id_concepto">
                                             <i class="fa fa-file-code"></i> Cargar x CFDI
                                         </button>
                                     </div>
                                 </div>
                                 <hr />
                                 <div class="row">
                                     <div  class="col-md-12 table-responsive-xl">
                                         <div>
                                             <table class="table table-bordered table-sm">
                                                 <thead>
                                                 <tr>
                                                     <th class="index_corto">#</th>
                                                     <th>Concepto</th>
                                                     <th class="c70">Cantidad</th>
                                                     <th class="c70">Precio</th>
                                                     <th class="c100">Monto</th>
                                                     <th class="c100"  v-if="con_descuento">Descuento</th>
                                                     <th class="c250">Destino</th>
                                                     <th class="icono">
                                                         <button type="button" class="btn btn-success btn-sm" v-if="cargando"  title="Cargando..." :disabled="cargando">
                                                             <i class="fa fa-spin fa-spinner"></i>
                                                         </button>
                                                         <button type="button" class="btn btn-success btn-sm" @click="addPartidas()" v-else>
                                                             <i class="fa fa-plus"></i>
                                                         </button>
                                                     </th>
                                                 </tr>
                                                 </thead>
                                                 <tbody>
                                                 <tr v-for="(partida, i) in partidas">
                                                     <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                     <td   v-if="partida.id_concepto_sat>0">
                                                         <c-f-d-i v-bind:id="partida.id_cfdi" v-bind:txt="partida.referencia" :key="i"></c-f-d-i>
                                                     </td>
                                                     <td  v-else>
                                                         <input type="text"
                                                                :readonly="partida.id_concepto_sat>0"
                                                                class="form-control"
                                                                :name="`referencia[${i}]`"
                                                                data-vv-as="Concepto"
                                                                v-validate="{required: true}"
                                                                :class="{'is-invalid': errors.has(`referencia[${i}]`)}"
                                                                v-model="partida.referencia"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`referencia[${i}]`)">{{ errors.first(`referencia[${i}]`) }}</div>
                                                     </td>
                                                     <td style="text-align: right"  v-if="partida.id_concepto_sat>0">
                                                         {{partida.cantidad}}
                                                     </td>
                                                     <td  v-else>
                                                         <input type="number"
                                                                min="0.01"
                                                                step=".01"
                                                                class="form-control"
                                                                :name="`cantidad[${i}]`"
                                                                data-vv-as="Cantidad"
                                                                style="text-align:right;"
                                                                v-validate="{required: true}"
                                                                :class="{'is-invalid': errors.has(`cantidad[${i}]`)}"
                                                                v-model="partida.cantidad"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`cantidad[${i}]`)">{{ errors.first(`cantidad[${i}]`) }}</div>
                                                     </td>
                                                     <td style="text-align: right"  v-if="partida.id_concepto_sat>0">
                                                         ${{parseFloat(partida.precio).formatMoney(2,'.',',')}}
                                                     </td>

                                                     <td v-else>
                                                         <input type="number"
                                                                :readonly="partida.id_concepto_sat>0"
                                                                min="0.01"
                                                                step=".01"
                                                                class="form-control"
                                                                :name="`precio[${i}]`"
                                                                data-vv-as="Precio"
                                                                style="text-align:right;"
                                                                v-validate="{required: true}"
                                                                :class="{'is-invalid': errors.has(`precio[${i}]`)}"
                                                                v-model="partida.precio"/>
                                                         <div class="invalid-feedback" v-show="errors.has(`precio[${i}]`)">{{ errors.first(`precio[${i}]`) }}</div>
                                                     </td>
                                                     <td style="text-align: right"  v-if="partida.id_concepto_sat>0">
                                                         ${{parseFloat(partida.monto).formatMoney(2,'.',',')}}
                                                     </td>
                                                     <td style="text-align: right" v-else>${{parseFloat(monto(partida, i)).formatMoney(2,'.',',')}}</td>

                                                     <td style="text-align: right" v-if="con_descuento">
                                                         ${{parseFloat(partida.descuento).formatMoney(2,'.',',')}}
                                                     </td>

                                                     <td >
                                                         <span v-if="partida.cambio_concepto == 0">
                                                            <span class="underline-cursored" v-on:click="seleccionarDestino(i)">{{partida.destino}}</span>
                                                         <div class="error-label" v-show="errors.has(`id_concepto[${i}]`)">{{ errors.first(`id_concepto[${i}]`) }}</div>
                                                         </span>
                                                         <span v-else-if="partida.cambio_concepto == 1">
                                                             <span class="underline-cursored" v-on:click="seleccionarDestino(i)"> Seleccionar Destino</span>
                                                         </span>
                                                         <span v-else>
                                                             <ConceptoSelectHijo
                                                                 :name="`id_concepto[${i}]`"
                                                                 data-vv-as="Concepto"
                                                                 id="id_concepto"
                                                                 v-model="partida.id_concepto"
                                                                 ref="conceptoSelectHijo"
                                                                 :disableBranchNodes="true"
                                                                 v-bind:nivel_id="id_concepto"
                                                                 :placeholder="partida.destino != ''?partida.destino:'--- Concepto ----'"
                                                                 v-validate="{required: true}"
                                                                 :class="{'is-invalid': errors.has(`id_concepto[${i}]`)}"
                                                             ></ConceptoSelectHijo>
                                                         <div class="error-label" v-show="errors.has(`id_concepto[${i}]`)">{{ errors.first(`id_concepto[${i}]`) }}</div>
                                                         </span>

                                                    </td>
                                                    <td >
                                                        <button  type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                 </tr>
                                                 </tbody>
                                             </table>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-9">
                                         <div class="form-group row error-content">
                                             <label for="observaciones" class="col-form-label">Observaciones: </label>
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
                                         <label>Total de CFDI Cargados:&nbsp;</label><span style="font-size: 15px; font-weight: bold">{{no_cfdi}}</span>
                                     </div>
                                     <div class="col-md-3" style="text-align: right">
                                         <div class="table-responsive col-md-12">
                                             <div class="col-md-12">

                                                 <table class="table table-borderless">
                                                     <tbody>
                                                         <tr>
                                                             <th style="text-align: left">Subtotal:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>${{(parseFloat(sumaMontos)).formatMoney(2,'.',',')}}</b></td>
                                                         </tr>
                                                         <tr  v-if="con_descuento">
                                                             <th style="text-align: left">Descuento:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>${{(parseFloat(sumaDescuentos)).formatMoney(2,'.',',')}}</b></td>
                                                         </tr>
                                                         <tr>
                                                             <th style="text-align: left">IVA:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>${{(parseFloat(iva)).formatMoney(2,'.',',')}}</b></td>
                                                         </tr>
                                                         <tr style="text-align: right">
                                                             <th style="text-align: left">Total:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>${{(parseFloat(sumaTotal)).formatMoney(2,'.',',')}}</b></td>
                                                         </tr>
                                                     </tbody>
                                                 </table>

                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="salir">Cerrar</button>
                                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate"><i class="fa fa-save"></i> Guardar</button>
                             </div>
                         </form>
                     </div>
                 </div>
            </div>
        </nav>
        <div class="modal fade" ref="modal_cfdi" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-destino"> <i class="fa fa-file-code"></i> Seleccionar CFDI</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div >
                                            <div>
                                                <label class="col-form-label">Archivos de CFDI (XML):</label>
                                            </div>
                                            <div>
                                                <div class="form-group error-content" >
                                                    <input type="file" class="form-control" id="archivo" @change="onFileChange" multiple="multiple"
                                                           row="3"
                                                           v-validate="{ ext: ['xml'], size: 3072}"
                                                           name="archivo"
                                                           data-vv-as="Archivo de CFDI"
                                                           ref="archivo"
                                                           :class="{'is-invalid': errors.has('archivo')}"
                                                    >
                                                    <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (xml)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <cfdi-show v-bind:cfdi="cfdi" v-if="cfdi"></cfdi-show>
                                        <div class="card" v-else-if="cargando">
                                            <div class="card-body">
                                                <div >
                                                    <div class="row" >
                                                        <div class="col-md-1">
                                                            <div class="spinner-border text-success" role="status">
                                                               <span class="sr-only">Cargando...</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-11">
                                                            <h5>Procesando</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalCFDI"><i class="fa fa-close"  ></i> Cerrar</button>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        <div class="modal fade" ref="modal_destino" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-destino"> <i class="fa fa-sign-in"></i> Seleccionar Destino General</h5>
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
                                            <from-concepto-select
                                                v-if="id_concepto"
                                                v-bind:nivel_id="id_concepto"
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="id_concepto_temporal"
                                                :error="errors.has('id_concepto')"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="true"
                                            ></from-concepto-select>
                                            <div class="error-label" v-show="errors.has('id_concepto_temporal')">{{ errors.first('id_concepto_temporal') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  type="button"  class="btn btn-secondary" v-on:click="cerrarModalDestino"><i class="fa fa-close"  ></i> Cerrar</button>
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
import {ModelListSelect} from 'vue-search-select';
import ConceptoSelect from "../../cadeco/concepto/Select";
import FromConceptoSelect from "../../cadeco/concepto/SelectFromConcepto.vue";
import ConceptoSelectHijo from "../../cadeco/concepto/SelectHijo";
import CfdiShow from "../../fiscal/cfd/cfd-sat/Show";
import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "relacion-gastos-create",
    components: {CFDI, CfdiShow, ModelListSelect, Datepicker, es, ConceptoSelect, ConceptoSelectHijo, FromConceptoSelect},
    data() {
        return {
            cargando : false,
            es : es,
            fechasDeshabilitadas : {},
            fecha : '',
            fecha_hoy : '',
            id_empresa : '',
            empresas: [],


            id_fondo : '',
            referencia : '',
            id_concepto : '',
            fondos : [],
            observaciones : '',
            partidas: [],
            subtotal : 0,
            iva : 0,
            total : 0,
            descuento : 0,
            archivo:null,
            archivo_name:null,
            files : [],
            names : [],
            cfdi : null,
            uuid : [],
            id_concepto_temporal:'',
            p_holder:'',
            destino:'',
            con_descuento: false
        }
    },
    computed: {
        sumaMontos() {
            let iva = 0;
            let result = 0;
            this.partidas.forEach(function (doc, i) {
                result += parseFloat(doc.monto);
                iva += parseFloat(doc.iva);
            })
            this.subtotal = result;
            this.iva = iva;
            return result
        },
        sumaDescuentos() {

            let result = 0;
            this.partidas.forEach(function (doc, i) {
                result += parseFloat(doc.descuento);
            })
            this.descuento = result;

            return result
        },
        sumaTotal() {
            this.total = parseFloat(parseFloat(this.subtotal).toFixed(2)) + parseFloat(parseFloat(this.iva).toFixed(2))
                - parseFloat(parseFloat(this.descuento).toFixed(2))
            return this.total
        },
        no_cfdi()
        {
            let cfdi = [];
            this.partidas.forEach(function (doc, i) {
                if(doc.id_cfdi>0)
                    cfdi.push(doc.id_cfdi)
            })

            let result_cfdi =cfdi.filter((item, index)=>{
                return cfdi.indexOf(item) === index;
            });

            return result_cfdi.length;
        }
    },
    mounted() {
        this.$validator.reset()
        this.fecha_hoy = new Date();
        this.fecha = new Date();
        this.fechasDeshabilitadas.from = new Date();
        this.id_fondo = ''
        this.getFondos();
    },
    methods : {
        init() {
            this.fecha = new Date();
            this.cargando = true;
        },
        descripcionFondo (item) {
            return `[${item.descripcion}]`
        },
        formatoFecha(date)
        {
            return moment(date).format('DD/MM/YYYY');
        },
        getEmpresas() {
            return this.$store.dispatch('controlRecursos/empresa/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:'activo'}
            })
                .then(data => {
                    this.empresas = data.data;
                })
        },
        getFondos() {
            return this.$store.dispatch('cadeco/fondo/index', {
                config: {
                    params: {
                        sort: 'descripcion',
                        order: 'asc'
                    }
                }
            }).then(fondos => {
                this.fondos = fondos.data;
            })
        },
        addPartidas(){
            this.partidas.splice(this.partidas.length + 1, 0, {
                referencia : "",
                cantidad : 0,
                precio : 0,
                monto : 0,
                iva : 0,
                id_concepto : "",
                id_concepto_sat : "",
                id_cfdi : "",
                uuid : "",
                cambio_concepto:1,
                destino:"",
                descuento:0,
            });
            this.index = this.index+1;
        },
        destroy(index){
            let _self = this;
            _self.con_descuento = false;
            if(this.partidas[index].id_concepto_sat > 0){
                let id_conceptos_sat_borrar = [];
                let id_cfdi = this.partidas[index].id_cfdi;
                let _self = this;
                let cfdi_borrar = [];
                this.partidas.forEach(function (partida, i) {
                    if(partida.id_cfdi == id_cfdi)
                    {
                        id_conceptos_sat_borrar.push(partida.id_concepto_sat);
                        cfdi_borrar.push(partida.uuid);
                    }
                });

                id_conceptos_sat_borrar.forEach(function (elemento, i){
                    _self.partidas.forEach(function (partida, i) {
                        if(partida.id_concepto_sat == elemento)
                        {
                            _self.partidas.splice(i, 1);
                        }
                    });
                });

                const cfdi_borrar_unicos = cfdi_borrar.filter((valor, indice) => {
                    return cfdi_borrar.indexOf(valor) === indice;
                });

                cfdi_borrar_unicos.forEach(function(partida, i){
                    let indice = _self.names.indexOf(partida+".xml");
                    _self.names.splice(indice, 1);
                    _self.files.splice(indice, 1);
                });

            }else {
                this.partidas.splice(index, 1);
            }
            _self.partidas.forEach(function (partida, i) {
                if(partida.descuento > 0)
                {
                    _self.con_descuento = true;
                }
            });
        },
        monto(partida, key) {
            var monto = 0;
            if(partida.cantidad != 0 && partida.precio != 0) {
                monto = parseFloat(partida.cantidad * partida.precio)
                this.partidas[key]['monto'] = monto;
            }
            return monto;
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result)
                {
                    if (this.partidas.length <= 0) {
                        swal('¡Error!', 'Debe ingresar al menos una partida.', 'error')
                    } else {
                        this.store();
                    }
                }
            });
        },
        store() {
            var datos = {};
            datos["fecha"] = this.$data.fecha;
            datos ["id_fondo"] = this.$data.id_fondo;
            datos ["referencia"] = this.$data.referencia;
            datos ["id_concepto"] = this.$data.id_concepto;
            datos ["observaciones"] = this.$data.observaciones;
            datos ["subtotal"] = this.$data.subtotal;
            datos ["iva"] = this.$data.iva;
            datos ["total"] = this.$data.total;
            datos ["partidas"] = this.$data.partidas;
            datos ["xmls"] = JSON.stringify(this.files);
            return this.$store.dispatch('finanzas/comprobante-fondo/store', datos)
                .then((data) => {
                    this.$emit('created', data)
                    this.salir();
                });
        },
        salir(){
            this.$router.push({name: 'comprobante-fondo'});
        },
        modalCFDI(){
            $(this.$refs.modal_cfdi).modal('show');
        },
        cerrarModalCFDI(){
            $(this.$refs.modal_cfdi).modal('hide');
        },
        modalDestino(){
            $(this.$refs.modal_destino).modal('show');
        },
        cerrarModalDestino(){
            $(this.$refs.modal_destino).modal('hide');
        },
        eliminarPartidasCFDI(){
            let id_conceptos_sat_borrar = [];
            let _self = this;
            this.partidas.forEach(function (partida, i) {
                if(partida.id_cfdi >0)
                {
                    id_conceptos_sat_borrar.push(partida.id_concepto_sat);
                }
            });
            id_conceptos_sat_borrar.forEach(function (elemento, i){
                _self.partidas.forEach(function (partida, i) {
                    if(partida.id_concepto_sat == elemento)
                    {
                        _self.partidas.splice(i, 1);
                    }
                });
            });
        },
        onFileChange(e){
            //this.files = [];
            this.eliminarPartidasCFDI();
            this.archivo = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;

            for(let i=0; i<files.length; i++) {
                this.archivo_name = files[i].name;
                this.createImage(files[i]);
                this.names.push(files[i].name);

                const unicos = this.names.filter((valor, indice) => {
                    return this.names.indexOf(valor) === indice;
                });
                this.names = unicos;

                if(files[i].type == "text/xml")
                {

                } else {
                    swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
                }
            }

            setTimeout(() => {
                this.cargarXML(1)
            }, 500);
        },
        agregaPartidasConConceptos(conceptos)
        {
            let _self = this;
            conceptos.forEach(function (concepto, i) {
                var busqueda = _self.partidas.find(x=>x.id_concepto_sat === concepto.id);
                if(busqueda == undefined)
                {
                    if(concepto.descuento>0)
                    {
                        _self.con_descuento = true;
                    }
                    _self.partidas.splice(_self.partidas.length + 1, 0, {
                        referencia : concepto.cfdi.serie + concepto.cfdi.folio + "-" + concepto.descripcion,
                        cantidad : concepto.cantidad,
                        precio : concepto.valor_unitario,
                        monto : concepto.importe,
                        iva : concepto.traslados[0] != undefined ? concepto.traslados[0].impuesto =="002" ? concepto.traslados[0].importe : 0 : 0,
                        id_concepto_sat : concepto.id,
                        id_concepto : "",
                        id_cfdi : concepto.id_cfd_sat,
                        uuid : concepto.cfdi.uuid,
                        cambio_concepto:1,
                        destino:"",
                        descuento:concepto.descuento,
                    });
                    _self.index = _self.index+1;
                }
            })
        },
        cargarXML(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('xmls',  JSON.stringify(this.files));
            formData.append('nombres_archivo',  JSON.stringify(this.names));
            return this.$store.dispatch('finanzas/cfdi-sat/cargarXMLComprobacion',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    var count = Object.keys(data).length;
                    if(count > 0){
                        this.agregaPartidasConConceptos(data);
                        $(this.$refs.modal_cfdi).modal('hide');
                        this.$refs.archivo.value = '';
                        this.archivo = null;
                    }else{
                        if(this.$refs.archivo.value !== ''){
                            this.$refs.archivo.value = '';
                            this.archivo = null;
                        }
                        this.cargado = false;
                        this.cleanData();
                        swal('Carga con XML', 'Archivo sin datos válidos', 'warning')
                    }
                })
                .catch(error => {
                    this.names = [];
                    this.files = [];
                    this.$refs.archivo.value = '';
                    this.archivo = null;
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.archivo = e.target.result;
                vm.files.push(e.target.result);
                const unicos = vm.files.filter((valor, indice) => {
                    return vm.files.indexOf(valor) === indice;
                });
                vm.files = unicos;
            };
            reader.readAsDataURL(file);
        },
        getConcepto() {
            return this.$store.dispatch('cadeco/concepto/find', {
                id: this.id_concepto_temporal,
                params: {
                }
            })
                .then(data => {
                    this.destino = data;
                    this.p_holder = data.clave_concepto_select + ' ' + data.descripcion;
                    this.replicarConcepto(data);
                })
        },
        replicarConcepto(data){
            let self = this;
            self.partidas.forEach(function (partida, i) {
                if(partida.id_concepto == ""){
                    partida.id_concepto = self.id_concepto_temporal;
                    partida.destino = data.clave_concepto_select + ' ' + data.descripcion;
                    partida.cambio_concepto = 0;
                }

            });
            self.cerrarModalDestino();
        },
        seleccionarDestino(i){
            this.partidas[i].cambio_concepto = 2;
        },
    },
    watch: {
        id_concepto_temporal(value){
            if(value !== '' && value !== null && value !== undefined){
                this.getConcepto();
            }
        },
    }
}
</script>

<style scoped>
th, label {
    color: #86888d;
}
.underline-cursored{
    cursor: pointer;
    text-decoration: underline;
}
</style>
