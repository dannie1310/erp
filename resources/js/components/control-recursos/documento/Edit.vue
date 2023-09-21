<template>
   <span>
       <div class="card" v-if="factura == null">
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
                                :error="errors.has('idserie')"
                                v-validate="{required: true}"
                                v-model="idserie">
                            <option value>-- Selecionar --</option>
                            <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group error-content">
                        <label for="tipo" class="col-form-label">Tipo:</label>
                        <select class="form-control"
                                data-vv-as="Tipo"
                                id="tipo"
                                name="tipo"
                                :error="errors.has('tipo')"
                                v-validate="{required: true}"
                                v-model="idtipodocto">
                            <option value>-- Selecionar --</option>
                            <option value="1">Documento para Solicitud de Pago de Orden de Compra</option>
                            <option value="6">Documento para Solicitud de Pago Recurrente</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                    </div>
                </div>
            </div>
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
                                v-model="factura.id_empresa">
                            <option value>-- Selecionar --</option>
                            <option v-for="(empresa) in empresas" :value="empresa.id">{{ empresa.razon_social }} - [ {{empresa.rfc}} ]</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_empresa')">{{ errors.first('id_empresa') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row error-content">
                        <label for="id_proveedor">Proveedor:</label>
                        <select v-if="!this.cargando_proveedores"
                            class="form-control"
                                data-vv-as="Proveedor"
                                id="id_proveedor"
                                name="id_proveedor"
                                :error="errors.has('id_proveedor')"
                                v-validate="{required: true}"
                                v-model="id_proveedor">
                            <option value>-- Selecionar --</option>
                            <option v-for="(proveedor) in proveedores" :value="proveedor.id">{{ proveedor.razon_social }} - [ {{proveedor.rfc}} ]</option>
                        </select>
                        <div v-else style="color:#5a6268;" class="form-control"><i class="fa fa-spinner fa-spin" /> Cargando Proveedores</div>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-2">
                     <div class="form-group error-content">
                         <label for="fecha">Fecha de Facturación:</label>
                         <datepicker v-model = "factura.fecha_editar"
                                     name = "fecha"
                                     :format = "formatoFecha"
                                     :language = "es"
                                     :bootstrap-styling = "true"
                                     class = "form-control"
                                     v-validate="{required: true}"
                                     :class="{'is-invalid': errors.has('fecha')}"
                         ></datepicker>
                         <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                     </div>
                 </div>
                <div class="col-md-2">
                    <div class="form-group error-content">
                        <label for="fecha">Fecha de Vencimiento:</label>
                        <datepicker v-model = "factura.vencimiento_editar"
                                    name = "vencimiento"
                                    :format = "formatoFecha"
                                    :language = "es"
                                    :bootstrap-styling = "true"
                                    class = "form-control"
                                    v-validate="{required: true}"
                                    :class="{'is-invalid': errors.has('vencimiento')}"
                        ></datepicker>
                        <div class="invalid-feedback" v-show="errors.has('vencimiento')">{{ errors.first('vencimiento') }}</div>
                    </div>
                </div>
                 <div class="col-md-2">
                     <div class="form-group error-content">
                         <label for="folio">Folio:</label>
                         <input
                             name="folio"
                             id="folio"
                             class="form-control"
                             v-model="factura.folio"
                             v-validate="{required: true}"
                             data-vv-as="Folio"
                             :class="{'is-invalid': errors.has('folio')}"
                         />
                     </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group error-content">
                        <label for="concepto">Concepto:</label>
                        <textarea
                            rows="2"
                            name="concepto"
                            id="concepto"
                            class="form-control"
                            v-model="factura.concepto"
                            v-validate="{required: true}"
                            data-vv-as="Concepto"
                            :class="{'is-invalid': errors.has('concepto')}"
                        />
                        <div class="invalid-feedback" v-show="errors.has('concepto')">{{ errors.first('concepto') }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <div class="form-group error-content float-right"><label for="importe">Importe:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">
                        <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                               name="subtotal"
                               data-vv-as="Importe"
                               v-on:keyup="calcularTotal"
                               v-model="importe"
                               style="text-align: right"
                               v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                               :class="{'is-invalid': errors.has(`subtotal`)}"
                               id="subtotal">
                        <div class="invalid-feedback" v-show="errors.has(`subtotal`)">{{ errors.first(`subtotal`) }}</div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group error-content float-right">
                        <label for="iva">IVA:
                            <select data-vv-as="IVA"
                                    id="iva"
                                    name="iva"
                                    :error="errors.has('iva')"
                                    v-validate="{required: true}"
                                    v-model="iva">
                                <option value="16">16</option>
                                <option value="11">11</option>
                                <option value="1">1</option>
                                <option value="0">0</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('iva')">{{ errors.first('iva') }}</div> %
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">
                        <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                               name="impuesto"
                               data-vv-as="Impuesto"
                               v-on:keyup="calcularImpuesto"
                               v-model="impuesto"
                               style="text-align: right"
                               v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                               :class="{'is-invalid': errors.has(`impuesto`)}"
                               id="impuesto">
                        <div class="invalid-feedback" v-show="errors.has(`impuesto`)">{{ errors.first(`impuesto`) }}</div>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group error-content float-right"><label for="retenciones">Retenciones:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">
                         <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                                name="retencion"
                                data-vv-as="Retención"
                                v-on:keyup="calcularTotal"
                                v-model="retencion"
                                style="text-align: right"
                                v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                                :class="{'is-invalid': errors.has(`retencion`)}"
                                id="impuesto">
                        <div class="invalid-feedback" v-show="errors.has(`retencion`)">{{ errors.first(`retencion`) }}</div>
                    </div>
                </div>
                <div class="col-md-5"></div>

                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="otros">Otros Impuestos:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">
                        <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm"
                               name="otros"
                               data-vv-as="Otros Impuesto"
                               v-on:keyup="calcularTotal"
                               v-model="otros"
                               style="text-align: right"
                               v-validate="{required: true, regex: /^[0-9]\d*(\.\d{0,2})?$/, min: 0.01, decimal:2}"
                               :class="{'is-invalid': errors.has(`otros`)}"
                               id="otros">
                        <div class="invalid-feedback" v-show="errors.has(`otros`)">{{ errors.first(`otros`) }}</div>
                    </div>
                </div>
                <div class="col-md-5"></div>

                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="total">Total:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{parseFloat(total).formatMoney(2)}} </div>
                </div>
                <div class="col-md-5"></div>

                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="moneda">Moneda:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">
                        <select class="form-control"
                                data-vv-as="Moneda"
                                id="id_moneda"
                                name="id_moneda"
                                :error="errors.has('id_moneda')"
                                v-validate="{required: true}"
                                v-model="factura.id_moneda">
                            <option value>-- Selecionar --</option>
                            <option v-for="(m) in monedas" :value="m.id">{{m.moneda}}</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
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
    name: "documento-edit",
    props: ['id'],
    components: {es,datepicker},
    data(){
        return{
            es: es,
            cargando: false,
            cargando_proveedores : false,
            factura : null,
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
        }
    },
    mounted() {
        this.getSeries();
        this.getEmpresas();
        this.getMonedas();
        this.find();
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/documento/find', {
                id: this.id,
                params:{include: []}
            }).then(data => {
                this.factura = data
                this.importe= this.factura.importe
                this.iva= this.factura.tasa_iva
                this.impuesto= this.factura.impuesto
                this.retencion= this.factura.retenciones
                this.otros= this.factura.otros
                this.total= this.factura.total
                this.idtipodocto = this.factura.id_tipo
                this.idserie = this.factura.id_serie
                this.id_proveedor = this.factura.id_proveedor
            }).finally(()=> {
                this.cargando = false;
            })
        },
        salir() {
            this.$router.push({name: 'documento'});
        },
        getSeries() {
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            }).then(data => {
                this.series = data.data;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(moment(this.factura.vencimiento_editar).format('YYYY/MM/DD') < moment(this.factura.fecha_editar).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha no puede ser posterior a la fecha de vencimiento.', 'error')
                    }else{
                        this.update()
                    }
                }
            });
        },
        update() {
            this.factura.importe = this.importe;
            this.factura.iva = this.iva;
            this.factura.impuesto = this.impuesto;
            this.factura.retencion = this.retencion;
            this.factura.otros = this.otros;
            this.factura.total = this.total;
            this.factura.id_tipo = this.idtipodocto;
            this.factura.estado = this.idtipodocto == 1 ? 1 : 5;
            this.factura.id_serie = this.idserie;
            this.factura.id_proveedor = this.id_proveedor;
            return this.$store.dispatch('controlRecursos/documento/update', {
                id: this.id,
                data: this.factura
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
            if(this.factura.id_serie != this.idserie)
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
        calcularImpuesto()
        {
            this.impuesto = ((parseFloat(this.importe) * parseFloat(this.iva)) / 100).toFixed(2);
        },
        calcularTotal()
        {
            this.total = (parseFloat(this.importe) + parseFloat(this.impuesto) + parseFloat(this.otros)) - parseFloat(this.retencion);
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
                this.calcularTotal();
            }
        },
        retencion(value)
        {
            if(value)
            {
                this.calcularTotal();
            }
        },
        otros(value)
        {
            if(value)
            {
                this.calcularTotal();
            }
        }
    }
}
</script>

<style scoped>

</style>
