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
            <form role="form" @submit.prevent="validate">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div class="row">
                                 <div class="col-md-2">
                                    <div class="form-group error-content">
                                        <label for="idserie" class="col-form-label">Serie:</label>
                                        <select class="form-control"
                                                data-vv-as="Serie"
                                                id="idserie"
                                                name="idserie"
                                                :class="{'is-invalid': errors.has('idserie')}"
                                                v-validate="{required: true}"
                                                v-model="idserie">
                                            <option value>-- Selecionar --</option>
                                            <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                                        </select>
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-2">
                                     <label class="col-form-label">Periodo:</label>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-3">
                                     <div class="form-group error-content">
                                         <label for="id_empleado">Fecha Inicial:</label>
                                         <datepicker v-model = "fecha_inicial"
                                                     name = "fecha_inicial"
                                                     :format = "formatoFecha"
                                                     data-vv-as="Fecha Inicial"
                                                     :language = "es"
                                                     :bootstrap-styling = "true"
                                                     class = "form-control"
                                                     v-validate="{required: true}"
                                                     :disabled-dates="fechasDeshabilitadas"
                                                     :class="{'is-invalid': errors.has('fecha_inicial')}"/>
                                         <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                                     </div>
                                 </div>
                                 <div class="col-md-3">
                                     <div class="form-group error-content">
                                         <label for="id_empleado">Fecha Final:</label>
                                         <datepicker v-model = "fecha_final"
                                                     name = "fecha_final"
                                                     data-vv-as="Fecha Final"
                                                     :format = "formatoFecha"
                                                     :language = "es"
                                                     :bootstrap-styling = "true"
                                                     class = "form-control"
                                                     v-validate="{required: true}"
                                                     :disabled-dates="fechasDeshabilitadas"
                                                     :class="{'is-invalid': errors.has('fecha_final')}"/>
                                         <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                                     </div>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group error-content">
                                         <label for="id_empresa">Empresa:</label>
                                         <select class="form-control"
                                            data-vv-as="Empresa"
                                            id="id_empresa"
                                            name="id_empresa"
                                            :class="{'is-invalid': errors.has('id_empresa')}"
                                            v-validate="{required: true}"
                                            v-model="id_empresa">
                                            <option value>-- Selecionar --</option>
                                            <option v-for="(empresa) in empresas" :value="empresa.id">{{ empresa.razon_social }} - [ {{empresa.rfc}} ]</option>
                                         </select>
                                         <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_proyecto">Proyecto:</label>
                                        <select class="form-control"
                                                data-vv-as="Proyecto"
                                                id="id_proyecto"
                                                name="id_proyecto"
                                                :class="{'is-invalid': errors.has('id_proyecto')}"
                                                v-validate="{required: true}"
                                                v-model="id_proyecto">
                                            <option value>-- Selecionar --</option>
                                            <option v-for="(p) in proyectos" :value="p.id">{{ p.ubicacion }}</option>
                                        </select>
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proyecto')">{{ errors.first('id_proyecto') }}</div>
                                    </div>
                                </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group error-content">
                                        <label for="id_empleado">Empleado:</label>
                                        <select class="form-control"
                                                data-vv-as="Empleado"
                                                id="id_empleado"
                                                name="id_empleado"
                                                :class="{'is-invalid': errors.has('id_empleado')}"
                                                v-validate="{required: true}"
                                                v-model="id_empleado">
                                            <option value>-- Selecionar --</option>
                                            <option v-for="(e) in empleados" :value="e.id">{{ e.razon_social }} - [ {{e.rfc}} ]</option>
                                        </select>
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empleado')">{{ errors.first('id_empleado') }}</div>
                                    </div>
                                </div>
                                 <div class="col-md-6" v-if="empleado != ''">
                                    <div class="form-group error-content">
                                        <label>Departamento:</label>
                                    </div>
                                     <div class="form-group error-content">
                                        <label>{{empleado.usuario.departamento.departamento}}</label>
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
                                 <div class="col-md-3">
                                     <div class="form-group error-content">
                                         <label for="moneda">Moneda:</label>
                                         <select class="form-control"
                                                data-vv-as="Moneda"
                                                id="id_moneda"
                                                name="id_moneda"
                                                :class="{'is-invalid': errors.has('id_moneda')}"
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
                                     <button type="button" class="btn btn-success btn-sm pull-right" @click="modalCFDI()">
                                         <i class="fa fa-file-code"></i> Cargar x CFDI
                                     </button>
                                 </div>
                             </div>
                             <hr />
                             <div class="row">
                                 <div  class="col-md-12 table-responsive-xl">
                                     <div class="table-responsive">
                                         <table class="table table-bordered table-sm">
                                             <thead>
                                             <tr>
                                                 <th class="index_corto">#</th>
                                                 <th class="c100">Tipo de Documento</th>
                                                 <th class="c80">Fecha</th>
                                                 <th class="c80">Folio</th>
                                                 <th class="c100">Concepto</th>
                                                 <th class="c100">Concepto del CFDI</th>
                                                 <th class="c100">Importe</th>
                                                 <th class="c100">IVA</th>
                                                 <th class="c100">Retenciones</th>
                                                 <th class="c100">Otros Imp.</th>
                                                 <th class="c100">Total</th>
                                                 <th class="c100">No. Personas</th>
                                                 <th class="c100">Observaciones</th>
                                                 <th class="icono">
                                                     <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                                         <i class="fa fa-plus"></i>
                                                     </button>
                                                 </th>
                                             </tr>
                                             </thead>
                                             <tbody>
                                             <tr v-for="(partida, i) in partidas">
                                                 <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                                 <td v-if="partida.uuid != null">
                                                     {{partida.tipo_documento}}
                                                 </td>
                                                 <td v-else>
                                                    <select class="form-control"
                                                            data-vv-as="Tipo"
                                                            id="tipo"
                                                            :name="`tipo[${i}]`"
                                                            :class="{'is-invalid': errors.has(`tipo[${i}]`)}"
                                                            v-validate="{required: true}"
                                                            v-model="partida.idtipo">
                                                        <option value>-- Selecionar --</option>
                                                        <option v-for="(t) in tipos" :value="t.id">{{ t.descripcion }}</option>
                                                    </select>
                                                     <div style="display:block" class="invalid-feedback" v-show="errors.has(`tipo[${i}]`)">{{ errors.first(`tipo[${i}]`) }}</div>
                                                 </td>
                                                 <td v-if="partida.uuid != null">
                                                     {{ partida.fecha }}
                                                 </td>
                                                 <td v-else>
                                                     <datepicker v-model = "partida.fecha"
                                                                 :name = "`fecha[${i}]`"
                                                                 :format = "formatoFecha"
                                                                 data-vv-as="Fecha"
                                                                 :language = "es"
                                                                 :bootstrap-styling = "true"
                                                                 class = "form-control"
                                                                 v-validate="{required: true}"
                                                                 :class="{'is-invalid': errors.has(`fecha[${i}]`)}"/>
                                                     <div class="invalid-feedback" v-show="errors.has(`fecha[${i}]`)">{{ errors.first(`fecha[${i}]`) }}</div>
                                                 </td>
                                                 <td v-if="partida.uuid != null">
                                                     {{ partida.folio }}
                                                 </td>
                                                 <td v-else>
                                                    <input class="form-control"
                                                           style="width: 100%"
                                                           placeholder="Folio"
                                                           :name="`folio[${i}]`"
                                                           id="folio"
                                                           data-vv-as="Folio"
                                                           v-validate="{required: true}"
                                                           v-model="partida.folio"
                                                           :class="{'is-invalid': errors.has(`folio[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`folio[${i}]`)">{{ errors.first(`folio[${i}]`) }}</div>
                                                 </td>
                                                 <td>
                                                     <select class="form-control"
                                                             data-vv-as="Tipo Gasto"
                                                             id="idtipogasto"
                                                             :name="`idtipogasto[${i}]`"
                                                             :class="{'is-invalid': errors.has(`idtipogasto[${i}]`)}"
                                                             v-validate="{required: true}"
                                                             v-model="partida.idtipogasto">
                                                        <option value>-- Selecionar --</option>
                                                        <option v-for="(t) in tipo_gastos" :value="t.id">{{ t.descripcion }}</option>
                                                    </select>
                                                     <div style="display:block" class="invalid-feedback" v-show="errors.has(`idtipogasto[${i}]`)">{{ errors.first(`idtipogasto[${i}]`) }}</div>
                                                 </td>
                                                 <td v-if="partida.uuid != null">
                                                     {{ partida.concepto }}
                                                 </td>
                                                 <td v-else>-</td>
                                                 <td style="text-align: right" v-if="partida.uuid != null">
                                                    {{ parseFloat(partida.importe).formatMoney(2) }}
                                                 </td>
                                                 <td v-else>
                                                     <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                                                            :name="`importe[${i}]`"
                                                            data-vv-as="Importe"
                                                            v-on:keyup="calcularTotalPorPartida(partida, i)"
                                                            v-model="partida.importe"
                                                            style="text-align: right"
                                                            v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                                            :class="{'is-invalid': errors.has(`importe[${i}]`)}"
                                                            id="importe">
                                                     <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                                 </td>
                                                 <td style="text-align: right" v-if="partida.uuid != null">
                                                    $ {{ parseFloat(partida.IVA).formatMoney(2)}}
                                                 </td>
                                                 <td style="text-align: right" v-else>$ 0.00</td>
                                                 <td style="text-align: right" v-if="partida.uuid != null">
                                                    $ {{ parseFloat(partida.retenciones).formatMoney(2) }}
                                                 </td>
                                                 <td style="text-align: right" v-else>
                                                    $ 0.00
                                                 </td>
                                                 <td style="text-align: right" v-if="partida.uuid != null">
                                                    $ {{ parseFloat(partida.otros_imp).formatMoney(2) }}
                                                 </td>
                                                 <td style="text-align: right" v-else>
                                                     $ 0.00
                                                 </td>
                                                 <td style="text-align: right">
                                                    $ {{ parseFloat(partida.total).formatMoney(2) }}
                                                 </td>
                                                 <td>
                                                      <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                                                             :name="`no_personas[${i}]`"
                                                             data-vv-as="No. Personas"
                                                             v-model="partida.no_personas"
                                                             style="text-align: right"
                                                             v-validate="{required: true, regex: /^[0-9]\d*?$/, min: 0.01, decimal:0}"
                                                             :class="{'is-invalid': errors.has(`no_personas[${i}]`)}"
                                                             id="no_personas">
                                                    <div class="invalid-feedback" v-show="errors.has(`no_personas[${i}]`)">{{ errors.first(`no_personas[${i}]`) }}</div>
                                                 </td>
                                                 <td>
                                                     <input class="form-control"
                                                            style="width: 100%"
                                                            placeholder="Observaciones"
                                                            :name="`observaciones[${i}]`"
                                                            id="observaciones"
                                                            data-vv-as="Observaciones"
                                                            v-validate="{required: true}"
                                                            v-model="partida.observaciones"
                                                            :class="{'is-invalid': errors.has(`observaciones[${i}]`)}">
                                                    <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                                 </td>
                                                 <td style="text-align: center">
                                                     <button  type="button" class="btn btn-outline-danger btn-sm" @click="destroy(i)"><i class="fa fa-trash"></i></button>
                                                 </td>
                                             </tr>
                                             </tbody>
                                         </table>
                                     </div>
                                 </div>
                             </div>
                             <div class="row">
                                     <div class="col-md-8">
                                         <label>Total de CFDI Cargados:&nbsp;</label><span style="font-size: 15px; font-weight: bold">{{no_cfd}}</span>
                                     </div>
                                     <div class="col-md-4" style="text-align: right">
                                         <div class="table-responsive col-md-12">
                                             <div class="col-md-12">
                                                 <table class="table table-borderless">
                                                     <tbody>
                                                         <tr>
                                                             <th style="text-align: left">Subtotal:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>$ {{parseFloat(sumaMontos).formatMoney(2) }}</b></td>
                                                         </tr>
                                                         <tr>
                                                             <th style="text-align: left">IVA:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>$ {{parseFloat(iva).formatMoney(2) }}</b></td>
                                                         </tr>
                                                         <tr>
                                                             <th style="text-align: left">Retenciones:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>$ {{parseFloat(sumaDescuentos).formatMoney(2) }}</b></td>
                                                         </tr>
                                                         <tr>
                                                             <th style="text-align: left">Otros Impuestos:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>$ {{parseFloat(sumaOtros).formatMoney(2) }}</b></td>
                                                         </tr>
                                                         <tr style="text-align: right">
                                                             <th style="text-align: left">Total:</th>
                                                             <td style="text-align: right; font-size: 15px"><b>$ {{parseFloat(sumaTotal).formatMoney(2)}}</b></td>
                                                         </tr>
                                                     </tbody>
                                                 </table>

                                             </div>
                                         </div>
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
                                                       :class="{'is-invalid': errors.has('archivo')}">
                                                <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (xml)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <cfdi-show v-bind:cfdi="cfdi" v-if="cfdi"></cfdi-show>
                                    <div class="card" v-else-if="cargando_a">
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
    </span>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
import CfdiShow from "../../../fiscal/cfd/cfd-sat/Show";
import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "relacion-gastos-create",
    components: { Datepicker, es, CFDI, CfdiShow},
    data() {
        return {
            cargando : false,
            cargando_a : false,
            es : es,
            fechasDeshabilitadas : {},
            fecha_hoy : '',
            id_empresa : '',
            empresas: [],
            id_empleado: '',
            empleados: [],
            empleado: '',
            departamento: '',
            fecha_inicial: '',
            fecha_final: '',
            motivo: '',
            id_moneda: '',
            monedas: [],
            id_proyecto: '',
            proyectos:[],
            tipos:[],
            tipo_gastos: [],
            partidas: [],
            no_cfd: 0,
            subtotal : 0,
            iva : 0,
            total : 0,
            descuento : 0,
            otros : 0,
            archivo:null,
            archivo_name:null,
            files : [],
            names : [],
            cfdi : null,
            uuid : [],
            p_holder:'',
            index : 0,
            observaciones: '',
            series: [],
            idserie: '',
            descuentos: 0
        }
    },
    computed: {
        sumaMontos()
        {
            let iva = 0;
            let result = 0;
            let descuentos = 0;
            this.partidas.forEach(function (doc, i) {
                result += parseFloat(doc.importe);
                iva += parseFloat(doc.IVA);
                descuentos += parseFloat(doc.descuento)
            })
            this.subtotal = result;
            this.iva = iva;
            this.descuentos = descuentos
            return result
        },
        sumaDescuentos()
        {
            let result = 0;
            this.partidas.forEach(function (doc, i) {
                result += parseFloat(doc.retenciones);
            })
            this.descuento = result;
            return result
        },
        sumaOtros()
        {
            let otros = 0;
            this.partidas.forEach(function (doc, i) {
                otros += parseFloat(doc.otros_imp);
            })
            this.otros = otros;
            return otros
        },
        sumaTotal() {
            this.total = (((parseFloat(this.subtotal) + parseFloat(this.iva)) - parseFloat(this.descuento)) + parseFloat(this.otros));
            return this.total
        },
        no_cfdi()
        {
            let suma = 0;
            this.partidas.forEach(function (doc, i) {
                if(doc.uuid != null)
                {
                   suma = parseInt(suma) + 1;
                }
            });
            this.no_cfd = suma;
        }
    },
    mounted() {
        this.$validator.reset()
        this.fecha_hoy = new Date();
        this.fecha_inicial = new Date();
        this.fecha_final = new Date();
        this.fechasDeshabilitadas.from = new Date();
        this.getEmpresas();
        this.getMonedas();
        this.getEmpleados();
        this.getProyectos();
        this.getTipos();
        this.getTiposGasto();
        this.getSeries();
    },
    methods : {
        init() {
            this.fecha_inicial = new Date();
            this.fecha_final = new Date();
            this.cargando = true;
        },
        formatoFecha(date) {
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
        getMonedas() {
            return this.$store.dispatch('controlRecursos/moneda/index', {
                params: {sort: 'orden', order: 'asc'}
            }).then(data => {
                this.monedas = data.data;
            })
        },
        getEmpleados() {
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: {
                    sort: 'RazonSocial', order: 'asc', scope: ['porTipos:2','porEstados:1', 'empleados']
                }
            }).then(data => {
                this.empleados = data.data;
            })
        },
        getProyectos() {
            return this.$store.dispatch('controlRecursos/ubicacion-relacion/index', {
                params: { }
            }).then(data => {
                this.proyectos = data.data;
            })
        },
        salir(){
            this.$router.push({name: 'relacion-gasto'});
        },
        buscarEmpleado(){
            return this.$store.dispatch('controlRecursos/proveedor/find', {
                id: this.id_empleado,
                params:{include: [ 'usuario.departamento' ]}
            }).then(data => {
                this.empleado = data
            })
        },
        getTipos() {
            return this.$store.dispatch('controlRecursos/tipo-doc-comp/index', {
                params: { }
            }).then(data => {
                this.tipos = data.data;
            })
        },
        getTiposGasto() {
            return this.$store.dispatch('controlRecursos/tipo-gasto-comp/index', {
                params: {
                    sort: 'Descripcion', order: 'asc', scope: ['porEstados:1']
                }
            }).then(data => {
                this.tipo_gastos = data.data;
            })
        },
        getSeries() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            })
                .then(data => {
                    this.series = data.data;
                    if(this.series.length == 1)
                    {
                        this.idserie = this.series[0].id;
                    }
                })
                .finally(() => {
                    this.cargando = false;
                })
        },
        addPartidas(){
            this.partidas.splice(this.partidas.length + 1, 0, {
                idtipo: "",
                tipo_documento: "",
                idtipogasto: '',
                fecha: this.fecha_hoy,
                folio : "",
                concepto : "",
                importe : 0,
                IVA : 0,
                retenciones : 0,
                otro_imp : 0,
                total: 0,
                no_personas: 1,
                observaciones: '',
                uuid : null,
                xml : '',
                contenido_xml: '',
                descuentos: 0
            });
            this.no_cfdi();
        },
        destroy(index){
            this.partidas.splice(index, 1);
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result)
                {
                    if(moment(this.fecha_final).format('YYYY/MM/DD') < moment(this.fecha_inicial).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de final no puede ser posterior a la fecha de inicial.', 'error')
                    }
                    else if (this.partidas.length == 0) {
                        swal('¡Error!', 'Debe ingresar al menos una partida.', 'error')
                    } else {
                        this.store();
                    }
                }
            });
        },
        store() {
            var datos = {};
            datos["fecha_inicial"] = this.$data.fecha_inicial;
            datos["fecha_final"] = this.$data.fecha_final;
            datos ["id_empresa"] = this.$data.id_empresa;
            datos ["id_empleado"] = this.$data.id_empleado;
            datos ["id_serie"] = this.$data.idserie;
            datos ["motivo"] = this.$data.motivo;
            datos ["id_moneda"] = this.$data.id_moneda;
            datos ["id_proyecto"] = this.$data.id_proyecto;
            datos ["subtotal"] = parseFloat(this.$data.subtotal);
            datos ["iva"] = parseFloat(this.$data.iva);
            datos ["total"] = parseFloat(this.$data.total);
            datos ["otros_imp"] = parseFloat(this.$data.otros);
            datos ["retenciones"] = parseFloat(this.$data.retenciones) ;
            datos["iddepartamento"] = this.$data.empleado.usuario.departamento.id;
            datos["descuentos"]= this.$data.descuentos;
            datos ["partidas"] = this.$data.partidas;
            return this.$store.dispatch('controlRecursos/relacion-gasto/store', datos)
                .then((data) => {
                    this.$emit('created', data)
                    this.salir();
                });
        },
        modalCFDI(){
            $(this.$refs.modal_cfdi).appendTo('body')
            $(this.$refs.modal_cfdi).modal('show');
        },
        cerrarModalCFDI(){
            $(this.$refs.modal_cfdi).modal('hide');
        },
        onFileChange(e){
            //this.files = [];
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
        agregaPartidasConConceptos(conceptos) {
            let _self = this;
            conceptos.forEach(function (concepto, i) {
                var busqueda = _self.partidas.find(x=>x.uuid === concepto.uuid);
                if(busqueda == undefined)
                {
                    _self.agregarCFDIPartida(concepto);
                }
            })
        },
        agregarCFDIPartida(concepto) {
            this.partidas.splice(this.partidas.length + 1, 0, {
                idtipo: 1,
                tipo_documento: 'Factura',
                tipo_comprobante:concepto.tipo_comprobante,
                idtipogasto: '',
                fecha: concepto.fecha_hora,
                folio : concepto.folio,
                concepto : concepto.conceptos[0].descripcion,
                importe : concepto.subtotal,
                IVA : concepto.importe_iva,
                retenciones : concepto.retenciones,
                otros_imp : concepto.otros_imp,
                total: concepto.total,
                no_personas: 1,
                observaciones: '',
                uuid : concepto.uuid,
                xml : concepto.xml,
                contenido_xml : concepto.contenido_xml,
                descuento : concepto.descuento
            });
            this.no_cfdi();
        },
        cargarXML(){
            this.cargando_a = true;
            var formData = new FormData();
            formData.append('xmls',  JSON.stringify(this.files));
            formData.append('nombres_archivo',  JSON.stringify(this.names));
            return this.$store.dispatch('finanzas/cfdi-sat/cargarXMLComprobacionRecursos',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    var count = Object.keys(data).length;
                    if((count > 0) == true){
                        this.agregaPartidasConConceptos(data);
                        this.$refs.archivo.value = '';
                        this.archivo = null
                        this.cerrarModalCFDI();
                    }else{
                        if(this.$refs.archivo.value !== ''){
                            this.$refs.archivo.value = '';
                            this.archivo = null;
                        }
                        this.cargado = false;
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
                    this.cargando_a = false;
                    this.cerrarModalCFDI();
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
        calcularTotalPorPartida(partida,i) {
            var total = 0;
            total = (parseFloat(partida.importe) + parseFloat(partida.IVA) + parseFloat(partida.otro_imp) - parseFloat(partida.retenciones));
            console.log(parseFloat(partida.importe),parseFloat(partida.IVA),parseFloat(partida.otro_imp),parseFloat(partida.retenciones), total);
            this.partidas[i]['total'] = total;
        },
    },
    watch: {
        id_empleado(value)
        {
            if(value)
            {
                this.buscarEmpleado();
            }
        }
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
