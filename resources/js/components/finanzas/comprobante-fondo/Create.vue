<template>
    <span>
        <nav>
            <div class="row">
                 <div class="col-12">
                     <div class="invoice p-3 mb-3">
                         <form role="form" @submit.prevent="validate">
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-2">
                                         <div class="form-group error-content">
                                             <label class="col-form-label">Fecha:</label>
                                             <datepicker v-model = "fecha"
                                                         name = "fecha"
                                                         :format = "formatoFecha"
                                                         :language = "es"
                                                         :bootstrap-styling = "true"
                                                         class = "form-control"
                                                         v-validate="{required: true}"
                                                         :disabled-dates="fechasDeshabilitadas"
                                                         :class="{'is-invalid': errors.has('fecha')}"
                                             />
                                             <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-7">
                                         <div class="form-group error-content">
                                            <label for="id_fondo">Fondo:</label>
                                               <model-list-select
                                                   name="id_fondo"
                                                   id="id_fondo"
                                                   placeholder="Seleccionar o buscar por descripción del fondo"
                                                   data-vv-as="Fondo"
                                                   v-model="id_fondo"
                                                   option-value="id"
                                                   v-validate="{required: true}"
                                                   :custom-text="descripcionFondo"
                                                   :list="fondos"/>
                                            <div class="error-label" v-show="errors.has('id_fondo')">{{ errors.first('id_fondo') }}</div>
                                        </div>
                                     </div>
                                     <div class="col-md-5">
                                    <div class="form-group error-content">
                                        <label for="referencia">Referencia:</label>
                                        <input
                                            type="text"
                                            name="referencia"
                                            data-vv-as="Referencia"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="referencia"
                                            placeholder="Referencia"
                                            v-model="referencia"
                                            :class="{'is-invalid': errors.has('referencia')}">
                                        <div class="error-label" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                    </div>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="col-md-12">
                                        <div class="form-group error-content">
                                            <label for="id_concepto">Centro de Costo:</label>
                                            <concepto-select
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="id_concepto"
                                                :error="errors.has('id_concepto')"
                                                v-validate="{required: true}"
                                                ref="conceptoSelect"
                                                :disableBranchNodes="false"
                                                :class="{'is-invalid': errors.has('id_concepto')}"
                                            />
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
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
                                 <div class="row" v-if="id_concepto">
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
                                                     <td style="text-align: right">${{parseFloat(monto(partida, i)).formatMoney(2,'.',',')}}</td>
                                                     <td >
                                                        <ConceptoSelectHijo
                                                            :name="`id_concepto[${i}]`"
                                                            data-vv-as="Concepto"
                                                            id="id_concepto"
                                                            v-model="partida.id_concepto"
                                                            ref="conceptoSelectHijo"
                                                            :disableBranchNodes="true"
                                                            v-bind:nivel_id="id_concepto"
                                                            placeholder="--- Concepto ----"
                                                            v-validate="{required: true}"
                                                            :class="{'is-invalid': errors.has(`id_concepto[${i}]`)}"
                                                        ></ConceptoSelectHijo>
                                                         <div class="error-label" v-show="errors.has(`id_concepto[${i}]`)">{{ errors.first(`id_concepto[${i}]`) }}</div>
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
    </span>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
import {ModelListSelect} from 'vue-search-select';
import ConceptoSelect from "../../cadeco/concepto/Select";
import ConceptoSelectHijo from "../../cadeco/concepto/SelectHijo";
import CfdiShow from "../../fiscal/cfd/cfd-sat/Show";
import CFDI from "../../fiscal/cfd/cfd-sat/CFDI";
export default {
    name: "comprobante-fondo-create",
    components: {CFDI, CfdiShow, ModelListSelect, Datepicker, es, ConceptoSelect, ConceptoSelectHijo},
    data() {
        return {
            cargando : false,
            es : es,
            fechasDeshabilitadas : {},
            fecha : '',
            fecha_hoy : '',
            id_fondo : '',
            referencia : '',
            id_concepto : '',
            fondos : [],
            observaciones : '',
            partidas: [],
            subtotal : 0,
            iva : 0,
            total : 0,
            archivo:null,
            archivo_name:null,
            files : [],
            names : [],
            cfdi : null,
            uuid : [],
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
        sumaTotal() {
            this.total = parseFloat(this.subtotal) + parseFloat(this.iva)
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
                uuid : ""
            });
            this.index = this.index+1;
        },
        destroy(index){
            if(this.partidas[index].id_concepto_sat > 0){
                let id_conceptos_sat_borrar = [];
                let id_cfdi = this.partidas[index].id_cfdi;
                let _self = this;
                this.partidas.forEach(function (partida, i) {
                    if(partida.id_cfdi == id_cfdi)
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

            }else {
                this.partidas.splice(index, 1);
            }
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
                }).finally(() => {
                    this.cargando = false;
                });
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.archivo = e.target.result;
                vm.files.push(e.target.result);
            };
            reader.readAsDataURL(file);
        },
    },
}
</script>

<style scoped>

th, label {
    color: #86888d;
}

</style>
