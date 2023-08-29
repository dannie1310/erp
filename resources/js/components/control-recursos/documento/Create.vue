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
                    <div class="col-md-12">
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
                </div>
                <div class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha de Facturación:</label>
                            <datepicker v-model = "fecha"
                                        name = "fecha"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        class = "form-control"
                                        v-validate="{required: true}"
                                        :disabled-dates="fechasDeshabilitadas"
                                        :class="{'is-invalid': errors.has('fecha')}"
                            ></datepicker>
                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group error-content">
                            <label for="fecha">Fecha Limite de Pago:</label>
                            <datepicker v-model = "vencimiento"
                                        name = "vencimiento"
                                        :format = "formatoFecha"
                                        :language = "es"
                                        :bootstrap-styling = "true"
                                        :disabled-dates="fechasDeshabilitadas"
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
                        <div class="form-group row error-content">
                            <label for="id_proveedor">Proveedor:</label>
                            <select class="form-control"
                                    data-vv-as="Proveedor"
                                    id="id_proveedor"
                                    name="id_proveedor"
                                    :error="errors.has('id_proveedor')"
                                    v-validate="{required: true}"
                                    v-model="id_proveedor">
                                <option value>-- Selecionar --</option>
                                <option v-for="(proveedor) in proveedores" :value="proveedor.id">{{ proveedor.razon_social }} - [ {{proveedor.rfc}} ]</option>
                            </select>
                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_proveedor')">{{ errors.first('id_proveedor') }}</div>
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
                        <div class="form-group error-content float-right"> {{parseFloat(subtotal).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group error-content float-right">
                            <div class="col-md-1">
                                <label for="iva">IVA:</label>
                            </div>
                            <div class="col-md-11">
                                <select class="form-control"
                                        data-vv-as="IVA"
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
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(impuesto).formatMoney(2)}} </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group error-content float-right"><label for="retenciones">Retenciones:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(retencion).formatMoney(2)}}</div>
                    </div>
                    <div class="col-md-5"></div>

                    <div class="col-md-5">
                        <div class="form-group error-content float-right"> <label for="otros">Otros Impuestos:</label></div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group error-content float-right"> {{parseFloat(otros).formatMoney(2)}} </div>
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
                            <label for="id_moneda">Moneda:</label>
                            <select class="form-control"
                                    data-vv-as="Moneda"
                                    id="id_moneda"
                                    name="id_moneda"
                                    :error="errors.has('id_moneda')"
                                    v-validate="{required: true}"
                                    v-model="id_moneda">
                                <option value>-- Selecionar --</option>
                                <option v-for="(m) in monedas" :value="m.id">{{m.moneda}}({{m.corto}})</option>
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
        this.getProveedores();
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
            return this.$store.dispatch('controlRecursos/proveedor/index', {
                params: {sort: 'RazonSocial', order: 'asc', scope:'porRFC'}
            }).then(data => {
                this.proveedores = data.data;
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
                    if(moment(this.data.vencimiento).format('YYYY/MM/DD') < moment(this.data.fecha).format('YYYY/MM/DD'))
                    {
                        swal('¡Error!', 'La fecha de facturación no puede ser posterior a la fecha de vencimiento.', 'error')
                    }else {
                        this.store()
                    }
                }
            });
        },
        store() {
            this.data.idserie = this.idserie;
            this.data.idtipodocto = this.idtipodocto;
            this.data.archivo = this.file_carga;
            this.data.nombre_archivo = this.file_carga_name;
            return this.$store.dispatch('controlRecursos/factura/store', this.$data.data)
                .then(data => {
                    this.salir();
                }).finally( ()=>{
                    this.cargando = false;
                });
        },
        salir()
        {
            this.$router.go(-1);
        },
    },
}
</script>

<style scoped>

</style>
