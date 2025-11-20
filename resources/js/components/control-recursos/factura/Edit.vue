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
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group error-content">
                        <h6 for="fecha"><b>Fecha de Facturación:</b></h6>
                        <h6>{{factura.fecha_format}}</h6>
                    </div>
                </div>
                 <div class="col-md-1">
                    <div class="form-group error-content">
                        <h6><b>Folio:</b></h6>
                        <h6>{{factura.folio}}</h6>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content">
                        <label for="idserie" class="col-form-label">Serie:</label>
                        <select class="form-control"
                                data-vv-as="Serie"
                                id="idserie"
                                name="idserie"
                                :error="errors.has('idserie')"
                                v-validate="{required: true}"
                                v-model="factura.id_serie">
                            <option value>-- Selecionar --</option>
                            <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group error-content">
                        <label for="tipo" class="col-form-label">Tipo de Documento:</label>
                        <select class="form-control"
                                data-vv-as="Tipo"
                                id="tipo"
                                name="tipo"
                                :error="errors.has('tipo')"
                                v-validate="{required: true}"
                                v-model="factura.id_tipo">
                            <option value>-- Seleccionar --</option>
                            <option value="1">Documento para Solicitud de Pago de Orden de Compra</option>
                            <option value="6">Documento para Solicitud de Pago Recurrente</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group error-content">
                        <h6><b>Empresa: </b></h6>
                        <h6>{{factura.empresa_descripcion}}</h6>
                    </div>
                </div>
            </div>
            <div class="row">
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
                <div class="col-md-6">
                    <div class="form-group error-content">
                        <h6><b>Proveedor:</b></h6>
                        <h6>{{factura.proveedor_descripcion}}</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check error-content">
                        <input type="checkbox" id="proveedor_extra" v-model="proveedor_extra" class="form-check-input">
                        <label class= "form-check-label" for="proveedor_extra"><b>Agregar proveedor emisor</b></label>
                    </div>
                </div>
                <div class="col-md-8"  v-if="proveedor_extra">
                    <div class="form-group error-content">
                        <label for="id_proveedor_ex">Proveedor original emitió factura:</label>
                        <select
                            v-if="!cargando_proveedores"
                            class="form-control"
                            :disabled="proveedores.length == 0 ? true : false"
                            data-vv-as="Proveedor"
                            id="id_proveedor_ex"
                            name="id_proveedor_ex"
                            :error="errors.has('id_proveedor_ex')"
                            v-validate="{required: true}"
                            v-model="id_proveedor_ex">
                             <option value v-if="!this.idserie">Seleccione la serie para cargar los proveedores</option>
                            <option value>-- Selecionar --</option>
                            <option v-for="(proveedor) in proveedores" :value="proveedor.id">{{ proveedor.razon_social }} - [ {{proveedor.rfc}} ]</option>
                        </select>
                        <div v-else style="color:#5a6268;" class="form-control"><i class="fa fa-spinner fa-spin" /> Cargando Proveedores</div>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor_ex')">{{ errors.first('id_proveedor_ex') }}</div>
                    </div>
                </div>
                <div class="col-md-6" v-else></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group error-content">
                        <label for="concepto">Concepto:</label>
                        <input
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
                    <div class="form-group error-content float-right"> {{factura.importe_format}} </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group error-content float-right"><label for="iva">IVA: {{factura.tasa_iva}} %:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{factura.iva_format}} </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group error-content float-right"><label for="descuento">Descuento:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{factura.descuento}} </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group error-content float-right"><label for="retenciones">Retenciones:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{factura.retencion_format}}</div>
                </div>
                <div class="col-md-5"></div>

                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="otros">Otros Impuestos:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{factura.otros}} </div>
                </div>
                <div class="col-md-5"></div>

                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="total">Total:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right"> {{factura.total_format}} </div>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="monesa">Moneda:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">{{factura.moneda}}  </div>
                </div>
                <div class="col-md-5"></div>
                <div class="col-md-5">
                    <div class="form-group error-content float-right"> <label for="tipo_cambio">TC:</label></div>
                </div>
                <div class="col-md-2">
                    <div class="form-group error-content float-right">{{factura.tc}}</div>
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
    name: "factura-edit",
    props: ['id'],
    components: {es,datepicker},
    data(){
        return{
            es: es,
            cargando: false,
            factura : null,
            series: [],
            proveedor_extra : false,
            id_proveedor_ex : '',
            id_proveedor: '',
            proveedores: [],
            cargando_proveedores : false,
        }
    },
    mounted() {
        this.getSeries();
        this.find();
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
        },
        find() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/factura/find', {
                id: this.id,
                params: { }
            }).then(data => {
                this.factura = data
                this.id_proveedor_ex = this.factura.id_proveedor_emisor
                this.proveedor_extra = this.factura.id_proveedor_emisor != null ? true : false
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
                    if(moment(this.factura.vencimiento_editar).format('YYYY/MM/DD') < moment(this.factura.fecha).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha no puede ser posterior a la fecha de vencimiento.', 'error')
                    }
                    else if (this.factura.solicitado){
                        swal('¡Error!', 'El documento se encuentra solicitado, no se puede editar.', 'error')
                    }
                    else if (this.factura.con_segmento){
                        swal('¡Error!', 'El documento se encuentra con segmentos de negocio cargados, no se puede editar.', 'error')
                    }
                    else{
                        this.update()
                    }
                }
            });
        },
        update() {
            this.factura.vencimiento = this.factura.vencimiento_editar;
            this.factura.id_proveedor = this.id_proveedor;
            this.factura.id_proveedor_emisor = this.proveedor_extra == true ? this.id_proveedor_ex : null;
            return this.$store.dispatch('controlRecursos/factura/update', {
                id: this.id,
                data: this.factura
            }).then((data) => {
                this.salir()
            })
        },
        getProveedores() {
            this.cargando_proveedores = true;
            if(this.factura.id_serie != this.idserie)
            {
                this.id_proveedor = "";
            }
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:['porTipos:1,3','porSerie:'+this.factura.id_serie, 'porEstados:1']}
            }).then(data => {
                this.proveedores = data.data;
            }).finally(() => {
                this.cargando_proveedores = false;
            })
        },
    },
    watch: {
        proveedor_extra(value)
        {
            if(value == true)
            {
                this.getProveedores();
            }
        },
        idserie(value) {
            if (value && this.proveedor_extra == true) {
                this.getProveedores();
            }
        },
    }
}
</script>

<style scoped>

</style>
