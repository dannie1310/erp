<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3" v-if="cargando">
                    <div class="row">
                        <div class="col-12">
                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                        </div>
                    </div>
                </div>
                <div class="invoice p-3 mb-3" v-if="subcontratos">
                    <div class="row">
                        <div class="col-6">
                            <h5>Subcontrato: <b>{{subcontratos.numero_folio_format}}</b></h5>
                        </div>
                        <div class="col-6">
                            <h5>Contrato: <b>{{subcontratos.contrato_folio_format}}</b></h5>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-7 mt-2 ml-3">
                            <div class="form-group  ">
                                <label for="razon_social" class="mr-1" ><b>Referencia: </b> </label>
                                <input
                                    style="text-transform:uppercase;"
                                    type="text"
                                    name="referencia"
                                    data-vv-as="Referencia"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="referencia"
                                    placeholder="Referencia"
                                    v-model="subcontratos.referencia"
                                    :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>

                                
                            </div>
                            
                        </div>
                        <div class="col-md-4 mt-2 ml-3">
                            <div class="form-group  ">
                                <label for="razon_social" class="mr-1" ><b>Fecha: </b> </label>
                                <datepicker v-model = "subcontratos.fecha"
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
                        <!-- <div class="table-responsive col-md-12">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="bg-gray-light" align="center" colspan="12"><b>{{subcontratos.empresa}}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Referencia:</b></td>
                                        <td class="bg-gray-light" colspan="9">
                                            <input
                                                    style="text-transform:uppercase;"
                                                    type="text"
                                                    name="referencia"
                                                    data-vv-as="Referencia"
                                                    v-validate="{required: true}"
                                                    class="form-control"
                                                    id="referencia"
                                                    placeholder="Referencia"
                                                    v-model="subcontratos.referencia"
                                                    :class="{'is-invalid': errors.has('referencia')}">
                                            <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                                        </td>
                                        <td class="bg-gray-light"><b>Fecha:</b></td>
                                        <td class="bg-gray-light">
                                            <datepicker v-model = "subcontratos.fecha"
                                                        name = "fecha"
                                                        :format = "formatoFecha"
                                                        :language = "es"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                            <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray-light"><b>Tipo de Gasto:</b></td>
                                        <td class="bg-gray-light">{{subcontratos.costo}}</td>
                                        <td class="bg-gray-light"><b>Tipo de Contrato:</b></td>
                                        <td class="bg-gray-light">{{subcontratos.tipo_subcontrato}}</td>
                                        <td class="bg-gray-light"><b>Personalidad Contratista:</b></td>
                                        <td class="bg-gray-light">{{subcontratos.personalidad_contratista}}</td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div> -->
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6><b>Detalle de las partidas</b></h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Clave</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(doc, i) in subcontratos.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td class="td_numero_folio"></td>
                                        <td>{{doc.contratos.descripcion}}</td>
                                        <td align="center">{{doc.contratos.unidad}}</td>
                                        <td class="td_money">{{doc.cantidad_format}}</td>
                                        <td class="td_money">{{doc.precio_unitario_format}}</td>
                                        <td class="td_money">{{doc.importe_total}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="row">
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Subtotal Antes Descuento:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_antes_descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Descuento(%):</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.descuento}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Subtotal:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.subtotal_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                name="iva"
                                v-model="subcontratos.impuesto"
                                v-validate="{required: true}"
                                class="col-sm-6 form-control"
                                id="iva"
                                :class="{'is-invalid': errors.has('iva')}">
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Retención IVA:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                name="retencion_iva"
                                v-model="subcontratos.retencion_iva"
                                v-validate="{required: true}"
                                class="col-sm-6 form-control"
                                id="retencion_iva"
                                :class="{'is-invalid': errors.has('retencion_iva')}">
                        </div>
                         <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Total:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.monto_format}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Moneda:</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.moneda.nombre}}</label>
                        </div>
                        <div class=" col-md-12" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">Anticipo({{subcontratos.anticipo_format}}):</label>
                            <label class="col-sm-2 col-form-label" style="text-align: right">{{subcontratos.anticipo_monto_format}}</label>
                        </div>
                        <div class=" col-md-10" align="right">
                            <label class="col-sm-2 col-form-label" style="text-align: right">% Fondo de Garantía:</label>
                        </div>
                        <div class=" col-md-2 p-1" align="right">
                            <input
                                type="number"
                                step="any"
                                max="100"
                                name="retencion_fg"
                                v-model="subcontratos.retencion_fg"
                                v-validate="{required: true}"
                                class="col-sm-6 form-control"
                                id="retencion_fg"
                                :class="{'is-invalid': errors.has('retencion_fg')}">
                        </div>
                        
                    </div>
                    <hr>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 mt-4 col-form-label" style="text-align: right">Plazo de Ejecución:</label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group  ">
                                <label for="fecha_ini" class="mr-1" ><b>De: </b> </label>
                                <datepicker v-model = "subcontratos.subcontratos.fecha_ini_ejec"
                                            name = "fecha_ini"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "col-sm-10 form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha_ini')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha_ini') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-2">
                            <div class="form-group  ">
                                <label for="fecha_fin" class="mr-1" ><b>Al: </b> </label>
                                <datepicker v-model = "subcontratos.subcontratos.fecha_fin_ejec"
                                            name = "fecha_fin"
                                            :format = "formatoFecha"
                                            :language = "es"
                                            :bootstrap-styling = "true"
                                            class = "col-sm-10 form-control"
                                            v-validate="{required: true}"
                                            :class="{'is-invalid': errors.has('fecha_fin')}"
                                ></datepicker>
                                <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha_fin') }}</div>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Descripción:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            <input
                                    style="text-transform:uppercase;"
                                    type="text"
                                    name="referencia"
                                    data-vv-as="Referencia"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="referencia"
                                    placeholder="Referencia"
                                    v-model="subcontratos.referencia"
                                    :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                    </div> <br>
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Tipo de Gasto:</label>
                        </div>
                        <div class=" col-md-4" align="left">
                            <input
                                    style="text-transform:uppercase;"
                                    type="text"
                                    name="referencia"
                                    data-vv-as="Referencia"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="referencia"
                                    placeholder="Referencia"
                                    v-model="subcontratos.referencia"
                                    :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Tipo de Contrato:</label>
                        </div>
                        <div class=" col-md-4" align="left">
                            <input
                                    style="text-transform:uppercase;"
                                    type="text"
                                    name="referencia"
                                    data-vv-as="Referencia"
                                    v-validate="{required: true}"
                                    class="form-control"
                                    id="referencia"
                                    placeholder="Referencia"
                                    v-model="subcontratos.referencia"
                                    :class="{'is-invalid': errors.has('referencia')}">
                                <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                    </div>
                    <!-- <td class="bg-gray-light"><b>Personalidad Contratista:</b></td>
                    <td class="bg-gray-light">{{subcontratos.personalidad_contratista}}</td> -->
                    <div class="row">
                        <div class=" col-md-2" align="left">
                            <label class="col-sm-12 col-form-label" style="text-align: right">Personalidad Contratista:</label>
                        </div>
                        <div class=" col-md-10" align="left">
                            <label class="col-form-label" style="text-align: left">{{subcontratos.personalidad_contratista}}</label>
                        </div>
                    </div>
                
               
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary float-right" style="margin-left:5px">Guardar</button>
                            <button type="button" class="btn btn-secondary float-right">Cerrar</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale';
    export default {
        name: "subcontrato-edit",
        components: {datepicker},
        props: ['id'],
        data() {
            return {
                es: es,
                cargando:false,
                subcontratos: '',
                tipo_gasto:[],
            }
        },
        mounted(){
            this.getTipoGasto();
            this.find();
        },
        methods: {
            formatoFecha(date){
                return moment(date).format('DD-MM-YYYY');
            },
            find() {
                this.cargando = true;
                this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id,
                    params: {include: ['partidas', 'moneda', 'partidas.contratos', 'subcontratos']}
                }).then(data => {
                    this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', data);
                    this.subcontratos = data;
                }).finally(()=>{
                    this.cargando = false;
                });
            },
            getTipoGasto(){
                this.tipo_gasto = [];
                return this.$store.dispatch('cadeco/costo/index', {
                        id: this.id,
                        params:{
                           sort: 'descripcion', order: 'ASC'
                        }
                    }).then(data => {
                        this.tipo_gasto = data.data;
                    })
            },
        }
    }
</script>

<style>

</style>