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
                <div class="col-md-1">
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
                <div class="col-md-6">
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
                params:{include: []}
            }).then(data => {
                this.factura = data
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
                    }else{
                        this.update()
                    }
                }
            });
        },
        update() {
            this.factura.vencimiento = this.factura.vencimiento_editar;
            return this.$store.dispatch('controlRecursos/factura/update', {
                id: this.id,
                data: this.factura
            }).then((data) => {
                this.salir()
            })
        },
    },
}
</script>

<style scoped>

</style>
