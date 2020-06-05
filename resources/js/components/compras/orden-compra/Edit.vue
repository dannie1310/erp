<template>  
    <span>
        <div class="row">
            <div class="col-12">    
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(orden_compra).length > 0">
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    {{orden_compra.solicitud.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-8" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    {{orden_compra.solicitud.observaciones}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Orden de Compra: </b></label>
                                    {{orden_compra.numero_folio_format}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Usuario Registro: </b></label>
                                    {{orden_compra.usuario.nombre}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Fecha Registro: </b></label>
                                    {{orden_compra.fecha_format}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Razón Social: </b></label>
                                    {{orden_compra.empresa.razon_social}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Sucursal: </b></label>
                                    {{orden_compra.sucursal.descripcion}}
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Dirección: </b></label>
                                    {{orden_compra.sucursal.direccion}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
             <div class="col-12" v-if="Object.keys(orden_compra).length > 0">
                
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Numero de Parte</th>
                                                <th>Descripción</th>
                                                <th>Unidad</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Descuento</th>
                                                <th>Importe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(partida, i) in orden_compra.partidas.data">
                                                <td>{{i+1}}</td>
                                                <td>{{partida.material.numero_parte}}</td>
                                                <td>{{partida.material.descripcion}}</td>
                                                <td>{{partida.material.unidad}}</td>
                                                <td>{{partida.cantidad}}</td>
                                                <td>{{partida.precio_unitario}}</td>
                                                <td>Descuento</td>
                                                <td>{{partida.importe}}</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Subtotal</td>
                                                <td>1000</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Impuesto</td>
                                                <td>1000</td>
                                            </tr>
                                            <tr>
                                                <td  colspan="6"></td>
                                                <td>Total</td>
                                                <td>1000</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
             <div class="col-12">    
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(orden_compra).length > 0">
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Pago en Parcialidades (%): </b></label>
                                    input
                                </div>
                            </div>

                            
                            <div class="col-md-8" >
                                <div class="form-group row error-content">
                                    <label for="tipo_gasto" class="col-sm-2 col-form-label">Tipo de Gasto: </label>
                                    <div class="col-sm-10">
                                        <model-list-select
                                            name="tipo_gasto"
                                            option-value="id"
                                            option-text="descripcion"
                                            :list="tipo_gasto"
                                            :placeholder="!cargando?'Seleccionar':'Cargando...'"
                                            :isError="errors.has(`tipo_gasto`)">
                                        </model-list-select>
                                        <div class="invalid-feedback" v-show="errors.has('tipo_gasto')">{{ errors.first('tipo_gasto') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>% Anticipo: </b></label>
                                    {{orden_compra.anticipo}}
                                </div>
                            </div>

                            <div class="col-md-8" >
                                <div class="form-group row error-content">
                                    <label for="forma_pago" class="col-sm-2 col-form-label">Forma de Pago: </label>
                                    <div class="col-sm-10">
                                        <select
                                            type="text"
                                            name="forma_pago"
                                            data-vv-as="Forma de Pago"
                                            v-validate="{required: true}"
                                            class="form-control"
                                            id="forma_pago"
                                            :class="{'is-invalid': errors.has('forma_pago')}"
                                        >
                                            <option value>-- Seleccione un conteo --</option>
                                            <option v-for="credito in formas_pago_credito" :value="credito.id">{{ credito.descripcion }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-show="errors.has('forma_pago')">{{ errors.first('forma_pago') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" >
                                <div class="form-group row error-content">
                                    <label for="fecha" class="col-sm-2 col-form-label">Fecha: </label>
                                    <div class="col-sm-10">
                                        <datepicker
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
                            </div>
                        

                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="plazo_entrega" class="col-sm-2 col-form-label">Plazo de Entrega/Ejecución:  </label>
                                    <div class="col-sm-10">
                                        <textarea
                                            name="plazo_entrega"
                                            id="plazo_entrega"
                                            class="form-control"
                                            v-validate="{required: true}"
                                            data-vv-as="Plazo de Entrega/Ejecución"
                                            :class="{'is-invalid': errors.has('plazo_entrega')}">
                                        </textarea>
                                        <div class="invalid-feedback" v-show="errors.has('plazo_entrega')">{{ errors.first('plazo_entrega') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="domicilio_entrega" class="col-sm-2 col-form-label">Domicilio de Entrega:  </label>
                                    <div class="col-sm-10">
                                        <textarea
                                            name="domicilio_entrega"
                                            id="domicilio_entrega"
                                            class="form-control"
                                            v-validate="{required: true}"
                                            data-vv-as="Domicilio de Entrega"
                                            :class="{'is-invalid': errors.has('domicilio_entrega')}">
                                        </textarea>
                                        <div class="invalid-feedback" v-show="errors.has('domicilio_entrega')">{{ errors.first('domicilio_entrega') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                             <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="otras_condiciones" class="col-sm-2 col-form-label">Otras Condiciones:  </label>
                                        <div class="col-sm-10">
                                            <textarea
                                                name="otras_condiciones"
                                                id="otras_condiciones"
                                                class="form-control"
                                                v-validate="{required: true}"
                                                data-vv-as="Otras Condiciones"
                                                :class="{'is-invalid': errors.has('otras_condiciones')}">
                                            </textarea>
                                            <div class="invalid-feedback" v-show="errors.has('otras_condiciones')">{{ errors.first('otras_condiciones') }}</div>
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
import datepicker from 'vuejs-datepicker';
import {es} from 'vuejs-datepicker/dist/locale';
export default {
    name: "orden-compra-edit",
    components: {ModelListSelect, datepicker},
    props: ['id'],
    data() {
        return {
            es: es,
            cargando: false,
            orden_compra:[],
            tipo_gasto:[],
            formas_pago_credito:[],
        }
    },
    mounted() {
        this.cargando = true;
        this.getTipoGasto();
        this.getOrdenCompra();
        this.getFormaPagoCredito();
    },
    methods: {
        formatoFecha(date){
            return moment(date).format('DD-MM-YYYY');
        },
        getOrdenCompra(){
            this.orden_compra = [];
            return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id,
                    params:{
                        include: ['empresa', 'sucursal', 'usuario', 'partidas.material', 'solicitud']
                    }
                }).then(data => {
                    this.orden_compra = data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        getTipoGasto(){
            this.tipo_gasto = [];
            return this.$store.dispatch('cadeco/costo/index', {
                    id: this.id,
                    params:{
                        scope: ['datosContablesConfiguracion'], sort: 'descripcion', order: 'ASC'
                    }
                }).then(data => {
                    this.tipo_gasto = data.data;
                })
        },
        getFormaPagoCredito(){
            this.tipo_gasto = [];
            return this.$store.dispatch('compras/forma-pago-credito/index', {
                    id: this.id,
                    params:{
                        scope: [], sort: 'id', order: 'ASC'
                    }
                }).then(data => {
                    this.formas_pago_credito = data.data;
                })
        },
    }

}
</script>

<style>

</style>