<template>
   <span>
       <div class="card" v-if="relacion == null">
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
           <div class="card-body">
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
                                    v-model="relacion.id_serie">
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
                            <datepicker v-model = "relacion.fecha_inicio"
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
                            <datepicker v-model = "relacion.fecha_final"
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
                                    v-model="relacion.id_empresa">
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
                                    v-model="relacion.id_proyecto">
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
                                    v-model="relacion.id_empleado">
                                <option value>-- Selecionar --</option>
                                <option v-for="(e) in empleados" :value="e.id">{{ e.razon_social }} - [ {{e.rfc}} ]</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empleado')">{{ errors.first('id_empleado') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <h6><b>Departamento:</b></h6>
                            <h6>{{relacion.departamento}}</h6>
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
                                v-model="relacion.motivo"
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
                                    v-model="relacion.id_moneda">
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
                                <tr v-for="(partida, i) in relacion.documentos.data">
                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                    <td v-if="partida.uuid != null">
                                        {{partida.tipoDocumento.descripcion}}
                                    </td>
                                    <td v-else>
                                        <select class="form-control" data-vv-as="Tipo"
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
                                        {{ partida.fecha_format }}
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
                                        {{ partida.concepto_xml }}
                                    </td>
                                    <td v-else>-</td>
                                    <td style="text-align: right" v-if="partida.uuid != null">
                                        {{ partida.importe_format }}
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
                                        {{ partida.iva_format}}
                                    </td>
                                    <td style="text-align: right" v-else>
                                        {{ partida.iva }}
                                    </td>
                                    <td style="text-align: right" v-if="partida.uuid != null">
                                        {{ partida.retenciones_format }}
                                    </td>
                                    <td v-else>
                                        <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                                               :name="`retencion[${i}]`"
                                               data-vv-as="Retención"
                                               v-on:keyup="calcularTotalPorPartida(partida,i)"
                                               v-model="partida.retenciones"
                                               style="text-align: right"
                                               v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                               :class="{'is-invalid': errors.has(`retencion[${i}]`)}"
                                               id="retencion">
                                        <div class="invalid-feedback" v-show="errors.has(`retencion[${i}]`)">{{ errors.first(`retencion[${i}]`) }}</div>
                                    </td>
                                    <td style="text-align: right" v-if="partida.uuid != null">
                                        {{ partida.otros_imp_format }}
                                    </td>
                                    <td v-else>
                                        <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                                               :name="`otro_imp[${i}]`"
                                               data-vv-as="Otro impuesto"
                                               v-on:keyup="calcularTotalPorPartida(partida,i)"
                                               v-model="partida.otro_imp"
                                               style="text-align: right"
                                               v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                               :class="{'is-invalid': errors.has(`otro_imp[${i}]`)}"
                                               id="otro_imp">
                                        <div class="invalid-feedback" v-show="errors.has(`otro_imp[${i}]`)">{{ errors.first(`otro_imp[${i}]`) }}</div>
                                    </td>
                                    <td style="text-align: right" v-if="partida.uuid != null">
                                        {{ partida.total_format }}
                                    </td>
                                    <td style="text-align: right" v-else>
                                        {{ partida.total }}
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
                                    <td>
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
                                v-model="relacion.motivo"
                                v-validate="{required: true}"
                                data-vv-as="Observaciones"
                                :class="{'is-invalid': errors.has('observaciones')}"
                            ></textarea>
                            <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                        </div>
                        <label>Total de CFDI Cargados:&nbsp;</label><span style="font-size: 15px; font-weight: bold">{{no_cfd}}</span>
                    </div>
                    <div class="col-md-3" style="text-align: right">
                        <div class="table-responsive col-md-12">
                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <tbody>
                                    <tr>
                                        <th style="text-align: left">Subtotal:</th>
                                        <td style="text-align: right; font-size: 15px"><b>{{sumaMontos}}</b></td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">IVA:</th>
                                        <td style="text-align: right; font-size: 15px"><b>${{iva}}</b></td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Retenciones:</th>
                                        <td style="text-align: right; font-size: 15px"><b>${{sumaDescuentos}}</b></td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: left">Otros Impuestos:</th>
                                        <td style="text-align: right; font-size: 15px"><b>${{sumaOtros}}</b></td>
                                    </tr>
                                    <tr style="text-align: right">
                                        <th style="text-align: left">Total:</th>
                                        <td style="text-align: right; font-size: 15px"><b>${{sumaTotal}}</b></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary"  v-on:click="salir">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" >
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
       </div>
   </span>
</template>

<script>
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "relacion-edit",
    props: ['id'],
    components: {es,datepicker},
    data(){
        return{
            es: es,
            fechasDeshabilitadas : {},
            fecha_hoy : '',
            fecha_inicial: '',
            fecha_final: '',
            cargando: false,
            cargando_proveedores : false,
            relacion : null,
            series: [],
            empresas: [],
            id_proveedor: '',
            proveedores: [],
            monedas: [],
            importe: 0,
            iva: 16,
            impuesto: 0,
            retencion: 0,
            otros: 0,
            total: 0,
            idtipodocto: '',
            idserie: '',
            proyectos: [],
            empleados: [],

        }
    },

    mounted() {
        this.fecha_hoy = new Date();
        this.fechasDeshabilitadas.from = new Date();
        this.relacion = null
        this.getSeries();
        this.getEmpresas();
        this.getMonedas();
        this.getEmpleados();
        this.getProyectos();
        this.getTipos();
        this.getTiposGasto();
        this.find();
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            return this.$store.dispatch('controlRecursos/relacion-gasto/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.relacion = data
                this.importe= data.importe
                this.iva= data.tasa_iva
                this.impuesto= data.impuesto
                this.retencion= data.retenciones
                this.otros= data.otros
                this.total= data.total
            }).finally(()=> {
                console.log(this.cargando)
                this.cargando = false;

                console.log("2",this.cargando)
            })
        },
        salir() {
            this.$router.push({name: 'documento'});
        },
        getSeries() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            }).then(data => {
                this.series = data.data;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(moment(this.relacion.fecha_inicio_format).format('YYYY/MM/DD') < moment(this.relacion.fecha_final_format).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha no puede ser posterior a la fecha de vencimiento.', 'error')
                    }else{
                        this.update()
                    }
                }
            });
        },
        update() {

            let importe_sin_comas;
            let impuesto_sin_comas;
            let otros_sin_comas;
            let retencion_sin_comas;
            let total_sin_comas;

            importe_sin_comas = this.importe.toString().replace(/,/g, '');
            impuesto_sin_comas = this.impuesto.toString().replace(/,/g, '');
            otros_sin_comas = this.otros.toString().replace(/,/g, '');
            retencion_sin_comas = this.retencion.toString().replace(/,/g, '');
            total_sin_comas = this.total.toString().replace(/,/g, '');

            this.relacion.importe = importe_sin_comas;
            this.relacion.iva = this.iva;
            this.relacion.impuesto = impuesto_sin_comas;
            this.relacion.retencion = retencion_sin_comas;
            this.relacion.otros = otros_sin_comas;
            this.relacion.total = total_sin_comas;
            this.relacion.id_tipo = this.idtipodocto;
            this.relacion.estado = this.idtipodocto == 1 ? 1 : 5;
            this.relacion.id_serie = this.idserie;
            this.relacion.id_proveedor = this.id_proveedor;
            return this.$store.dispatch('controlRecursos/documento/update', {
                id: this.id,
                data: this.relacion
            }).then((data) => {
                this.salir()
            })
        },
        getEmpresas() {
            return this.$store.dispatch('controlRecursos/empresa/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:'activo'}
            })
                .then(data => {
                    this.empresas = data.data;
                })
        },
        getProveedores() {
            this.cargando_proveedores = true;
            if(this.relacion.id_serie != this.idserie)
            {
                this.id_proveedor = "";
            }
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:['porTipos:1,3','porSerie:'+this.idserie, 'porEstados:1']}
            }).then(data => {
                this.proveedores = data.data;
            }).finally(() => {
                this.cargando_proveedores = false;
            })
        },
        getMonedas() {
            return this.$store.dispatch('controlRecursos/moneda/index', {
                params: {sort: 'orden', order: 'asc'}
            }).then(data => {
                this.monedas = data.data;
            })
        },
        calcularImpuesto() {
            let importe_sin_comas;
            importe_sin_comas = this.importe.toString().replace(/,/g, '');
            this.impuesto = ((parseFloat(importe_sin_comas) * parseFloat(this.iva)) / 100).toString().formatearkeyUp();
        },
        calcularTotal() {
            let importe_sin_comas;
            let impuesto_sin_comas;
            let otros_sin_comas;
            let retencion_sin_comas;

            importe_sin_comas = this.importe.toString().replace(/,/g, '');
            impuesto_sin_comas = this.impuesto.toString().replace(/,/g, '');
            otros_sin_comas = this.otros.toString().replace(/,/g, '');
            retencion_sin_comas = this.retencion.toString().replace(/,/g, '');

            this.total = (parseFloat(importe_sin_comas) + parseFloat(impuesto_sin_comas) + parseFloat(otros_sin_comas) - parseFloat(retencion_sin_comas)).toString().formatearkeyUp();
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
        addPartidas(){
            this.relacion.documentos.data.splice(this.relacion.documentos.data.length + 1, 0, {
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

            });
            //this.index = this.index+1;
        },
        destroy(index){
            this.relacion.documentos.data.splice(index, 1);
        },
        modalCFDI(){
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
                var busqueda = _self.relacion.documentos.data.find(x=>x.uuid === concepto.uuid);
                if(busqueda == undefined)
                {
                    _self.agregarCFDIPartida(concepto);
                }
            })
        },
        agregarCFDIPartida(concepto) {
            this.relacion.documentos.data.splice(this.relacion.documentos.data.length + 1, 0, {
                idtipo: 1,
                tipo_documento: 'Factura',
                tipo_comprobante:concepto.tipo_comprobante,
                idtipogasto: '',
                fecha: concepto.fecha_hora,
                folio : concepto.folio,
                concepto : concepto.conceptos[0].descripcion,
                importe : concepto.conceptos[0].importe,
                IVA : concepto.importe_iva.toString().formatearkeyUp(),
                retenciones : 0,
                otro_imp : 0,
                total: concepto.total.toString().formatearkeyUp(),
                no_personas: 1,
                observaciones: '',
                uuid : concepto.uuid,
                xml : concepto.xml,
                contenido_xml : concepto.contenido_xml
            });
        },
        cargarXML(){
            this.cargando = true;
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
                        this.cargando = false;
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
        calcularTotalPorPartida(partida,i) {
            var total = 0;
            var iva = 0;
            iva = ((parseFloat(partida.importe) * parseFloat(16)) / 100).toString().formatearkeyUp();
            this.relacion.documentos.data[i]['IVA'] = iva;
            total = (parseFloat(partida.importe) + parseFloat(iva) + parseFloat(partida.otro_imp) - parseFloat(partida.retenciones)).toString().formatearkeyUp();
            this.relacion.documentos.data[i]['total'] = total;
        },
    },
    watch: {
        idserie(value)
        {
            if(value)
            {
                this.getProveedores();
            }
        },
        importe(value) {
            if(value)
            {
                let cifra_formateada = 0;
                cifra_formateada = value.toString().formatearkeyUp();
                this.importe = cifra_formateada;
                this.calcularImpuesto();
                this.calcularTotal();
            }
        },
        iva(value)
        {
            if(value)
            {
                this.calcularImpuesto()
            }
        },
        impuesto(value) {
            if(value)
            {
                let cifra_formateada = 0;
                cifra_formateada = value.toString().formatearkeyUp();
                this.impuesto = cifra_formateada;
                this.calcularTotal();
            }
        },
        retencion(value)
        {
            if(value)
            {
                let cifra_formateada = 0;
                cifra_formateada = value.toString().formatearkeyUp();
                this.retencion = cifra_formateada;
                this.calcularTotal();
            }
        },
        otros(value)
        {
            if(value)
            {
                let cifra_formateada = 0;
                cifra_formateada = value.toString().formatearkeyUp();
                this.otros = cifra_formateada;
                this.calcularTotal();
            }
        }
    }
}
</script>

<style scoped>

</style>
