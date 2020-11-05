<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="id_solicitud">Seleccionar Contrato Proyectado:</label>
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_contrato"
                                        option-value="id_transaccion"                                                               
                                        v-model="id_contrato"
                                        :custom-text="numeroFolioFormatAndObservaciones"
                                        :list="contratos"
                                        :placeholder="!cargando?'Seleccionar o buscar Contrato Proyectado':'Cargando...'"
                                        :isError="errors.has(`id_contrato`)">
                                    </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_contrato')">{{ errors.first('id_contrato') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="data">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                         <thead>
                                            <tr>
                                                <th colspan="6" rowspan="2" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="9" >
                                                    <select
                                                        type="text"
                                                        name="id_empresa"
                                                        data-vv-as="Razón Social"
                                                        class="form-control"
                                                        id="id_empresa"
                                                        v-model="id_transaccion">
                                                        <option v-for="presupuesto in data.presupuestos" :value="presupuesto.id_transaccion">{{ presupuesto.razon_social }}</option>
                                                    </select>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 18%;">Descripción</th>
                                                <th style="width: 18%;">Destino</th>
                                                <th style="width: 4%;">Unidad</th>
                                                <th style="width: 6%;">Cantidad Solicitada</th>
                                                <th style="width: 6%;">Cantidad Pendiente Asignar</th>
                                             
                                                <th class="bg-gray-light ">Precio Unitario Antes Descto.</th>
                                                <th class="bg-gray-light ">Precio Total Antes Descto.</th>
                                                <th class="bg-gray-light">% Descuento</th>
                                                <th class="bg-gray-light ">Precio Unitario</th>
                                                <th class="bg-gray-light ">Precio Total</th>
                                                <th class="bg-gray-light">Moneda</th>
                                                <th class="bg-gray-light ">Precio Total Moneda Conversión</th>
                                                <th style="width: 6%;" class="bg-gray-light">Observaciones</th>
                                                <th class="bg-gray-light th_money_input">Cantidad Asignada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in data.items" v-if="item.item_pendiente">
                                                <td>{{ i+1}}</td>
                                                <td :title="item.descripcion">{{item.descripcion_corta}}</td>
                                                <td :title="item.destino">{{item.destino_corto}}</td>
                                                <td>{{item.unidad}}</td>
                                                <td>{{item.cantidad_solicitada}}</td>
                                                <td>{{item.cantidad_disponible}}</td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].precio_unitario}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].precio_total_antes_desc}}</td><td v-else></td>
                                                <td v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].descuento}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]">$ {{data.presupuestos[id_transaccion].partidas[i].precio_unitario_con_desc}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]">$ {{data.presupuestos[id_transaccion].partidas[i].precio_total_con_desc}}</td><td v-else></td>
                                                <td v-if="data.presupuestos[id_transaccion].partidas[i]">{{data.presupuestos[id_transaccion].partidas[i].moneda}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]">$ {{data.presupuestos[id_transaccion].partidas[i].importe_moneda_conversion}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.presupuestos[id_transaccion].partidas[i]"> {{data.presupuestos[id_transaccion].partidas[i].observaciones}}</td><td v-else></td>
                                                <td>
                                                    <span  v-if="data.presupuestos[id_transaccion].partidas[i]">
                                                        <input v-on:change="recalcular(i)"
                                                            type="number"
                                                            :disabled="item.cantidad_disponible == 0 && data.presupuestos[id_transaccion].partidas[i].cantidad_asignada == ''"
                                                            
                                                            class="form-control"
                                                            :name="`cantidad_asignada[${item.id_concepto}]`"
                                                            data-vv-as="Cantidad Asignada"
                                                            v-model="data.presupuestos[id_transaccion].partidas[i].cantidad_asignada"
                                                            v-validate="{max_value:item.cantidad_base, min_value:0}"
                                                            :class="{'is-invalid': errors.has(`cantidad_asignada[${item.id_concepto}]`)}"
                                                            id="cantidad_asignada">
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_asignada[${item.id_concepto}]`)">{{ errors.first(`cantidad_asignada[${item.id_concepto}]`) }}</div>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" @click="borrarVolumenes()" class="btn btn-default pull-right" style="margin-left:5px">Borrar los Volúmenes del Proveedor</button>
                                <button type="button" @click="cargarVolumenes()" class="btn btn-default pull-right">Cargar Todos los Volúmenes a Proveedor</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">
                            <i class="fa fa-angle-left"></i>
                            Regresar
                        </button>
                        <button type="button" @click="validate()" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
import {ModelListSelect} from 'vue-search-select';
export default {
    name: "asignacion-proveedores-create",
    components: {ModelListSelect},
    data() {
        return {
            cargando: false,
            contratos:[],
            id_contrato:'',
            data:null,
            id_transaccion:'',
        }
    },
    mounted() {
        this.getContratos();
    },
    computed: {
        
    },
    methods: {
        cerrar(){
            swal({
                title: "Cerrar Asignación de Contratistas",
                text: "¿Está seguro/a de que quiere salir del registro de asignación de contratistas?",
                icon: "info",
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        visible: true
                    },
                    confirm: {
                        text: 'Si, Salir',
                        closeModal: true,
                    }
                }
            })
            .then((value) => {
                if (value) {
                    this.$router.push({name: 'asignacion-contratista'});
                }
            });
        },
        cargarVolumenes(){
            let self = this;
            Object.entries(self.data.items).forEach(([i, item], index) => {
                if(item.cantidad_disponible > 0){
                    self.data.presupuestos[self.id_transaccion].partidas[i]? self.data.presupuestos[self.id_transaccion].partidas[i].cantidad_asignada = item.cantidad_disponible:'';
                    self.data.presupuestos[self.id_transaccion].partidas[i]?item.cantidad_disponible = parseFloat(0).toFixed(4):'';
                }
            });
        },
        borrarVolumenes(){
            let self = this;
            Object.entries(self.data.items).forEach(([i, item], index) => {   
                if(self.data.presupuestos[self.id_transaccion].partidas[i]){
                    if(self.data.presupuestos[self.id_transaccion].partidas[i].cantidad_asignada > 0){
                        self.data.presupuestos[self.id_transaccion].partidas[i].cantidad_asignada = '';
                        item.cantidad_disponible = parseFloat(item.cantidad_base).toFixed(4);
                    }
                }
            });
        },
        numeroFolioFormatAndObservaciones(item){
            return `[${item.folio}] - [${item.referencia}]`
        },
        getContratos(){
            this.cargando = true;
            return this.$store.dispatch('contratos/contrato-proyectado/getContratos', {
                params: this.query
            })
            .then(data => {
                this.contratos =  data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        getCotizaciones(id){
            this.cargando = true;
            this.data = null;
            return this.$store.dispatch('contratos/contrato-proyectado/getCotizaciones', {
                id: id,
                params: {}
            })
            .then(data => {
                this.id_transaccion = Object.keys(data.presupuestos)[0];
                this.data = data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        recalcular(i){
            let asignadas = 0.0;

            Object.values(this.data.presupuestos).forEach(partida =>{
                if(partida.partidas[i] && partida.partidas[i].cantidad_asignada !== ''){
                    asignadas = +asignadas + +partida.partidas[i].cantidad_asignada;
                }                   
            });

            if(asignadas > this.data.items[i].cantidad_base){
                if(this.data.items[i].cantidad_disponible === 0){
                    swal('¡Aviso!', 'El item ya no tiene cantidad pendiente por asignar', 'warning');
                }else{
                    swal('¡Aviso!', 'La cantidad asignada es mayor a la pendiente por asignar.', 'warning');
                }
                asignadas = +asignadas - +this.data.presupuestos[this.id_transaccion].partidas[i].cantidad_asignada;
                this.data.presupuestos[this.id_transaccion].partidas[i].cantidad_asignada = '';
            }else{
                let p_unitario = 0;
                p_unitario = this.data.presupuestos[this.id_transaccion].partidas[i].precio_unitario_con_desc;
                    
                let c_asignada =this.data.presupuestos[this.id_transaccion].partidas[i].cantidad_asignada !== ''?this.data.presupuestos[this.id_transaccion].partidas[i].cantidad_asignada:0;
                this.data.presupuestos[this.id_transaccion].partidas[i].precio_total_con_desc = parseFloat(p_unitario * c_asignada).formatMoney(2);
                this.data.presupuestos[this.id_transaccion].partidas[i].importe_moneda_conversion = parseFloat((p_unitario * c_asignada) * this.data.presupuestos[this.id_transaccion].partidas[i].tipo_cambio).formatMoney(2);
     
            }
            // this.data.items[i].cantidad_disponible = this.data.items[i].cantidad_base;
            this.data.items[i].cantidad_disponible = parseFloat(this.data.items[i].cantidad_base - asignadas).toFixed(4);

        },
        salir(){
            swal({
                title: "Salir de Asignación de Contratista",
                text: "¿Está seguro de que quiere salir del registro de asignación de contratistas?",
                icon: "info",
                buttons: {
                    cancel: {
                        text: 'Cancelar',
                        visible: true
                    },
                    confirm: {
                        text: 'Si, Salir',
                        closeModal: true,
                    }
                }
            })
            .then((value) => {
                if (value) {
                    this.$router.push({name: 'asignacion-contratista'});
                }
            });
        },
        store() {
            this.cargando = true;
            return this.$store.dispatch('contratos/asignacion-contratista/store', {
                id_contrato:this.id_contrato,
                presupuestos:this.data.presupuestos
            })
            .then((data) => {
                this.$router.push({name: 'asignacion-contratista'});
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    if(this.validarVolumenItems()){
                        this.store();
                    }else{
                        swal({
                            title: "Asignación de Contratista",
                            text: "Debe asignar al menos una partida.",
                            icon: "warning",
                            buttons: {
                                cancel: {
                                    text: 'Cerrar',
                                    visible: true
                                },
                            }
                        })
                    }
                    
                }
            });
        },
        validarVolumenItems(){
            let self = this;
            let resp = false;
            Object.entries(this.data.presupuestos).forEach(([i, presupuesto], index) => { 
                presupuesto.partidas.forEach(function (partida, i){
                    if(partida.cantidad_asignada > 0){
                        console.log(partida.cantidad_asignada);
                        resp = true;
                    }
                })                   
            });
            return resp;
        },
    },
    watch:{
        id_contrato(value){
            if(value != ''){
                this.getCotizaciones(value);
            }
        }
    }


}
</script>

<style>

</style>