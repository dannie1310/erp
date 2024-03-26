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

                    <div class="col-md-3">
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
                        <div class="form-group error-content">
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
                        <div class="form-group error-content">
                            <label for="id_proveedor">Proveedor:</label>
                            <select
                                v-if="!cargando_proveedores"
                                class="form-control"
                                    :disabled="proveedores.length == 0 ? true : false"
                                    data-vv-as="Proveedor"
                                    id="id_proveedor"
                                    name="id_proveedor"
                                    :error="errors.has('id_proveedor')"
                                    v-validate="{required: true}"
                                    v-model="id_proveedor">
                                 <option value v-if="!this.idserie">Seleccione la serie para cargar los proveedores</option>
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
                            <label for="folio">Folio:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Folio"
                                   name="folio"
                                   id="folio"
                                   data-vv-as="Folio"
                                   v-validate="{required: true}"
                                   v-model="folio"
                                   :class="{'is-invalid': errors.has('folio')}">
                            <div class="invalid-feedback" v-show="errors.has('folio')">{{ errors.first('folio') }}</div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha de Emisión:</label>
                            <datepicker v-model = "fecha"
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
                            <label for="fecha">Fecha Limite de Pago:</label>
                            <datepicker v-model = "vencimiento"
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
                                v-model="concepto"
                                v-validate="{required: true}"
                                data-vv-as="Concepto"
                                :class="{'is-invalid': errors.has('concepto')}" />
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
                                   v-model="subtotal"
                                   style="text-align: right"
                                   v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
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
                                        v-on:keyup="calcularImpuesto"
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
                                   v-model="impuesto"
                                   style="text-align: right"
                                   v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
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
                                    v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
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
                                   v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
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
                        <div class="form-group error-content float-right"> {{total}} </div>
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
                                    v-model="id_moneda">
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
    name: "factura-create",
    components: {es,datepicker},
    data() {
        return {
            es: es,
            cargando: false,
            cargando_proveedores : false,
            folio: '',
            empresas: [],
            id_empresa: '',
            proveedores: [],
            id_proveedor: '',
            series: [],
            idserie: '',
            idtipodocto: '',
            fechasDeshabilitadas:{},
            vencimiento: '',
            fecha: '',
            monedas: [],
            id_moneda: 3,
            concepto: '',
            subtotal: 0,
            iva: 16,
            impuesto: 0,
            retencion: 0,
            otros: 0,
            total: 0
        }
    },
    mounted() {
        this.$validator.reset()
        this.getEmpresas();
        this.getMonedas();
        this.getSeries();
        this.fechasDeshabilitadas.from = new Date();
        this.fecha = new Date();
        this.vencimiento = new Date();
    },
    methods : {
        formatoFecha(date){
            return moment(date).format('DD/MM/YYYY');
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
            this.id_proveedor = "";
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
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    if(moment(this.vencimiento).format('YYYY/MM/DD') < moment(this.fecha).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de facturación no puede ser posterior a la fecha de vencimiento.', 'error')
                    }else {
                        this.store()
                    }
                }
            });
        },
        store() {
            return this.$store.dispatch('controlRecursos/documento/store',
                {
                    folio: this.folio,
                    id_empresa: this.id_empresa,
                    id_proveedor: this.id_proveedor,
                    idserie: this.idserie,
                    idtipodocto: this.idtipodocto,
                    fecha: moment(this.fecha).format('YYYY-MM-DD'),
                    vencimiento:  moment(this.vencimiento).format('YYYY-MM-DD'),
                    id_moneda: this.id_moneda,
                    concepto: this.concepto,
                    subtotal: parseFloat(this.subtotal.toString().replace(/,/g, '')),
                    iva: this.iva,
                    impuesto: parseFloat(this.impuesto.toString().replace(/,/g, '')),
                    retencion: parseFloat(this.retencion.toString().replace(/,/g, '')),
                    otros: parseFloat(this.otros.toString().replace(/,/g, '')),
                    total: parseFloat(this.total.toString().replace(/,/g, '')),
                    estado: this.idtipodocto == 1 ? 1 : 5,
                })
                .then(data => {
                    this.salir();
                }).finally( ()=>{
                    this.cargando = false;
                });
        },
        salir()
        {
            this.$router.push({name: 'documento'});
        },
        /*calcularImpuesto()
        {
            this.impuesto = ((parseFloat(this.subtotal) * parseFloat(this.iva)) / 100).toFixed(2);
        },
        calcularTotal()
        {
            this.total = (parseFloat(this.subtotal) + parseFloat(this.impuesto) + parseFloat(this.otros)) - parseFloat(this.retencion);
        },*/
        calcularImpuesto()
        {
            let subtotal_sin_comas;
            subtotal_sin_comas = this.subtotal.toString().replace(/,/g, '');
            this.impuesto = ((parseFloat(subtotal_sin_comas) * parseFloat(this.iva)) / 100).toFixed(2).toString().formatearkeyUp();
        },
        calcularTotal()
        {
            let subtotal_sin_comas;
            let impuesto_sin_comas;
            let otros_sin_comas;
            let retencion_sin_comas;

            subtotal_sin_comas = this.subtotal.toString().replace(/,/g, '');
            impuesto_sin_comas = this.impuesto.toString().replace(/,/g, '');
            otros_sin_comas = this.otros.toString().replace(/,/g, '');
            retencion_sin_comas = this.retencion.toString().replace(/,/g, '');

            this.total = (parseFloat(subtotal_sin_comas) + parseFloat(impuesto_sin_comas) + parseFloat(otros_sin_comas) - parseFloat(retencion_sin_comas)).toFixed(2).toString().formatearkeyUp();
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
        subtotal(value) {
            if(value)
            {
                let cifra_formateada = 0;
                cifra_formateada = value.toString().formatearkeyUp();
                this.subtotal = cifra_formateada;
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
