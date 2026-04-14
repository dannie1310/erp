<template>
   <span>
       <div class="card" v-if="comprobacion == null">
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
                   <div class="col-md-6">
                       <div class="form-group error-content">
                           <label>Empresa:</label>
                           <h6>{{comprobacion.empresa_descripcion}}</h6>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-group error-content">
                           <label class="col-form-label">Departamento:</label>
                           <h6>{{comprobacion.departamento}}</h6>
                       </div>
                   </div>
                   <div class="col-md-3">
                       <div class="form-group error-content">
                           <label class="col-form-label">Solitud de Pago:</label>
                           <h6>{{comprobacion.folio_solicitud}}</h6>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-6">
                       <div class="form-group error-content">
                           <label class="col-form-label">Empleado:</label>
                           <h6>{{comprobacion.empleado_descripcion}} </h6>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="form-group error-content">
                           <label class="col-form-label">Obra / Proyecto:</label>
                           <h6>{{comprobacion.proyecto_descripcion}} </h6>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-2">
                       <div class="form-group error-content">
                           <label class="col-form-label">Fecha:</label>
                           <h6>{{comprobacion.fecha_format}} </h6>
                       </div>
                   </div>
                   <div class="col-md-4">
                       <div class="form-group error-content">
                           <label for="fecha_inicial">Del:</label>
                           <datepicker v-model = "comprobacion.del_editar"
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
                   <div class="col-md-4">
                       <div class="form-group error-content">
                           <label for="fecha_final">Al:</label>
                           <datepicker v-model = "comprobacion.al_editar"
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
                   <div class="col-md-8">
                       <div class="form-group error-content">
                           <label for="motivo">Motivo:</label>
                           <input
                               name="motivo"
                               id="motivo"
                               class="form-control"
                               v-model="comprobacion.motivo"
                               v-validate="{required: true}"
                               data-vv-as="Motivo"
                               :class="{'is-invalid': errors.has('motivo')}" />
                           <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-2">
                       <div class="form-group error-content">
                           <label class="col-form-label">Importe:</label>
                           <h6>{{comprobacion.importe_format}} </h6>
                       </div>
                   </div>
                   <div class="col-md-2">
                       <div class="form-group error-content">
                           <label class="col-form-label">Comprobado:</label>
                           <h6>{{ sumaMontos.total_doc.formatMoney(2) }}</h6>
                       </div>
                   </div>
                   <div class="col-md-2">
                       <div class="form-group error-content">
                           <label class="col-form-label">Saldo:</label>
                           <h6>{{ sumaTotal.formatMoney(2) }}</h6>
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
                                    <th class="index_corto encabezado">#</th>
                                    <th class="c100 encabezado">Tipo de Documento</th>
                                    <th class="c80 encabezado">Fecha</th>
                                    <th class="c80 encabezado">Folio</th>
                                    <th class="c100 encabezado">Concepto</th>
                                    <th class="c100 encabezado">Concepto del CFDI</th>
                                    <th class="c100 encabezado">Importe</th>
                                    <th class="c100 encabezado">IVA</th>
                                    <th class="c100 encabezado">2% H</th>
                                    <th class="c100 encabezado">Retención</th>
                                    <th class="c100 encabezado">Total</th>
                                    <th class="c100 encabezado">Observaciones</th>
                                    <th class="c100 encabezado">Uuid</th>
                                    <th class="icono encabezado">
                                        <button type="button" class="btn btn-success btn-sm" @click="addPartidas()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(partida, i) in documentos">
                                    <td style="text-align:center; vertical-align:inherit;">{{i+1}}</td>
                                    <td v-if="partida.uuid != null">
                                        Factura
                                    </td>
                                    <td v-else>
                                        <select class="form-control" data-vv-as="Tipo"
                                                id="tipo"
                                                :name="`tipo[${i}]`"
                                                :class="{'is-invalid': errors.has(`tipo[${i}]`)}"
                                                v-validate="{required: true}"
                                                v-model="partida.tipo_docto_comp">
                                            <option value>-- Selecionar --</option>
                                            <option v-for="(t) in tipos" :value="t.id">{{ t.descripcion }}</option>
                                        </select>
                                        <div style="display:block" class="invalid-feedback" v-show="errors.has(`tipo[${i}]`)">{{ errors.first(`tipo[${i}]`) }}</div>
                                    </td>
                                    <td v-if="partida.uuid != null">
                                        {{ partida.fecha_editar != undefined ?  partida.fecha_editar :  partida.fecha}}
                                    </td>
                                    <td v-else>
                                        <datepicker v-model = "partida.fecha_editar"
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
                                        {{ partida.folio_docto }}
                                    </td>
                                    <td v-else>
                                        <input class="form-control"
                                               style="width: 100%"
                                               placeholder="Folio"
                                               :name="`folio[${i}]`"
                                               :id="`folio[${i}]`"
                                               data-vv-as="Folio"
                                               v-validate="{required: true}"
                                               v-model="partida.folio_docto"
                                               :class="{'is-invalid': errors.has(`folio[${i}]`)}">
                                        <div class="invalid-feedback" v-show="errors.has(`folio[${i}]`)">{{ errors.first(`folio[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <select class="form-control"
                                                data-vv-as="Tipo Gasto"
                                                :id="`idtipogasto[${i}]`"
                                                :name="`idtipogasto[${i}]`"
                                                :class="{'is-invalid': errors.has(`idtipogasto[${i}]`)}"
                                                v-validate="{required: true}"
                                                v-model="partida.tipo_gasto">
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
                                               :id="`importe[${i}]`">
                                        <div class="invalid-feedback" v-show="errors.has(`importe[${i}]`)">{{ errors.first(`importe[${i}]`) }}</div>
                                    </td>
                                    <td style="text-align: right" v-if="partida.uuid != null">
                                        $ {{ parseFloat(partida.iva).formatMoney(2)}}
                                    </td>
                                    <td style="text-align: right" v-else>
                                       $ 0.00
                                    </td>
                                    <td style="text-align: right">
                                        $
                                    </td>
                                     <td style="text-align: right" v-if="partida.uuid != null">
                                         {{ parseFloat(partida.retencion).formatMoney(2)}}
                                    </td>
                                    <td style="text-align: right" v-else>
                                       $ 0.00
                                    </td>
                                    <td style="text-align: right">
                                        $ {{ parseFloat(partida.total).formatMoney(2) }}
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               style="width: 100%"
                                               placeholder="Observaciones"
                                               :name="`observaciones[${i}]`"
                                               :id="`observaciones[${i}]`"
                                               data-vv-as="Observaciones"
                                               v-validate="{required: true}"
                                               v-model="partida.observaciones"
                                               :class="{'is-invalid': errors.has(`observaciones[${i}]`)}">
                                        <div class="invalid-feedback" v-show="errors.has(`observaciones[${i}]`)">{{ errors.first(`observaciones[${i}]`) }}</div>
                                    </td>
                                    <td v-if="partida.uuid != null">
                                        {{ partida.uuid }}
                                    </td>
                                    <td v-else>
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
                    <div class="col-md-8">
                        <label>Total de documentos cargados:&nbsp;</label><span style="font-size: 15px; font-weight: bold">{{total_documentos}}</span>
                    </div>
                    <div class="col-md-4">
                        <label>Total de CFDI cargados:&nbsp;</label><span style="font-size: 15px; font-weight: bold">{{no_cfd}}</span>
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
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
import estado from './partials/EstatusLabel';
import CfdiShow from "../../../fiscal/cfd/cfd-sat/Show";
import CFDI from "../../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "comprobacion-gasto-edit",
    props: ['id'],
    components: {es,datepicker, estado, CFDI, CfdiShow},
    data(){
        return{
            es: es,
            fecha_hoy : '',
            fechasDeshabilitadas : {},
            cargando: false,
            cargando_a: false,
            comprobacion : null,
            tipos:[],
            tipo_gastos: [],
            documentos: [],
            empresas: [],
            id_proveedor: '',
            proveedores: [],
            monedas: [],
            subtotal: 0,
            iva: 0,
            retencion: 0,
            otros: 0,
            total: 0,
            idtipodocto: '',
            idserie: '',
            proyectos: [],
            empleados: [],
            cfdi : null,
            archivo:null,
            archivo_name:null,
            files : [],
            names : [],
            uuid : [],
            p_holder:'',
            index : 0,
            descuentos : 0
        }
    },

    mounted() {
        this.fecha_hoy = new Date();
        this.comprobacion = null
        this.find();
        this.getTipos();
        this.getTiposGasto();
    },
    computed: {
        sumaMontos() {
            let iva = 0;
            let result = 0;
            let total_doc = 0;

            this.documentos.forEach(doc => {
                result += parseFloat(doc.importe || 0);
                total_doc += parseFloat(doc.total || 0);
                if (doc.uuid) {
                    iva += parseFloat(doc.iva || 0);
                }
            });

            return { importe: result, iva, total_doc };
        },
        sumaTotal() {
            if (!this.comprobacion) return 0;
            return (
                parseFloat(this.comprobacion.importe || 0)  -
                this.sumaMontos.total_doc
            );
        },
        no_cfd() {
            return this.documentos.filter(doc => doc.uuid != null).length;
        },
        total_documentos() {
            return this.documentos.length;
        }
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.comprobacion = null
            return this.$store.dispatch('controlRecursos/comprobacion/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.comprobacion = data
                if(data.documentos != undefined) {
                    this.documentos = data.documentos.data;
                }else {
                    this.addPartidas();
                }
              //  this.total_documentos = this.documentos.length
            }).finally(()=> {
                this.cargando = false;
            })
        },
        salir() {
            this.$router.push({name: 'comprobacion-gasto'});
        },
        getTipos() {
            return this.$store.dispatch('controlRecursos/tipo-doc-comp/index', {
                params: { }
            }).then(data => {
                this.tipos = data.data;
            })
        },
        validate() {
            const importeAutorizado = parseFloat(this.comprobacion.importe || 0);
            const importeCapturado = parseFloat(this.sumaMontos.importe || 0);
            this.$validator.validate().then(result => {
                if (result) {
                    if(moment(this.comprobacion.al_editar).format('YYYY/MM/DD') < moment(this.comprobacion.del_editar).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha final no puede ser anterior a la fecha inicial.', 'error')
                    }
                    else if (this.documentos.length == 0) {
                        swal('¡Error!', 'Debe ingresar al menos una partida.', 'error')
                    }
                    else{
                        this.update()
                    }
                }
            });
        },
        update() {
            this.comprobacion.importe_calculado = this.sumaMontos.importe;
            this.comprobacion.total_comprobado = this.sumaMontos.total_doc;
            this.comprobacion.total_calculado = this.sumaTotal;
            this.comprobacion.documentos = this.documentos;
            return this.$store.dispatch('controlRecursos/comprobacion/update', {
                id: this.id,
                data: this.comprobacion
            }).then((data) => {
                this.salir()
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
            this.documentos.push({
                tipo_docto_comp: "",
                tipo_documento: "",
                tipo_gasto: '',
                tipo_comprobante: '',
                fecha_editar: this.fecha_hoy,
                folio_docto : "",
                concepto : "",
                importe : 0,
                iva : 0,
                total: 0,
                uuid : null,
                xml : '',
                contenido_xml: '',
                descuento: '',
                retencion: 0
            });
           // this.total_documentos = this.total_documentos + 1;
        },
        destroy(index){
            let _self = this;
            var partida = this.documentos[index]
            let x = 0;
            if(partida.uuid != null) {
                this.names.forEach(function (name, i) {
                    let indice = _self.names.indexOf(partida.uuid + ".xml");
                    _self.names.splice(indice, 1);
                    _self.files.splice(indice, 1);
                });
           //     this.no_cfd = this.no_cfd - 1;
            }
            this.documentos.splice(index, 1);
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

            conceptos.forEach(function (concepto) {
                var busqueda = _self.documentos.find(x=>x.uuid === concepto.uuid);
                if(busqueda == undefined)
                {
                    _self.agregarCFDIPartida(concepto);
                }
            });
        },
        agregarCFDIPartida(concepto) {
            this.documentos.splice(this.documentos.length + 1, 0, {
                tipo_docto_comp: 1,
                tipo_documento: 'Factura',
                tipo_comprobante:concepto.tipo_comprobante,
                tipo_gasto: '',
                fecha_editar: concepto.fecha_hora,
                folio_docto : concepto.folio,
                concepto : concepto.conceptos[0].descripcion,
                importe : concepto.subtotal,
                iva : concepto.importe_iva,
                retenciones : concepto.retenciones,
                retencion : concepto.retencion,
                otros_imp : concepto.otros_imp,
                total: concepto.total,
                observaciones: '',
                uuid : concepto.uuid,
                xml : concepto.xml,
                contenido_xml : concepto.contenido_xml,
                descuento : concepto.descuento - concepto.descuento_IEPS
            });
        },
        cargarXML(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('xmls',  JSON.stringify(this.files));
            formData.append('nombres_archivo',  JSON.stringify(this.names));
            formData.append('id_comprobacion',  JSON.stringify(this.id));
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
            if(partida.uuid === null)
            {
                this.documentos[i]['iva'] = 0;
                this.documentos[i]['otros_imp'] = 0;
                this.documentos[i]['retenciones'] = 0;
            }
            total = (parseFloat(partida.importe) + parseFloat(partida.iva));
            this.documentos[i]['total'] = total;
        },
        getEstado(estado, color) {
            return {
                color: color,
                descripcion: estado
            }
        },
    }
}
</script>

<style scoped>

</style>
