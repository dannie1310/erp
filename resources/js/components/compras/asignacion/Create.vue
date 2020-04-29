<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="id_solicitud">Seleccionar Solicitud de Compra:</label>
                                    <model-list-select
                                        :disabled="cargando"
                                        name="id_solicitud"
                                        option-value="id"                                                               
                                        v-model="id_solicitud"
                                        option-text="observaciones"
                                        :list="solicitudes"
                                        :placeholder="!cargando?'Seleccionar o buscar material por descripcion':'Cargando...'"
                                        :isError="errors.has(`id_solicitud`)">
                                    </model-list-select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_solicitud')">{{ errors.first('id_solicitud') }}</div>
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
                                                <th colspan="6" rowspan="4" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >
                                                    <select
                                                        type="text"
                                                        name="id_empresa"
                                                        data-vv-as="Razón Social"
                                                        class="form-control"
                                                        id="id_empresa"
                                                        v-model="id_empresa">
                                                        <option v-for="cotizacion in data.cotizaciones" :value="cotizacion.id_transaccion">{{ cotizacion.razon_social }}</option>
                                                    </select>
                                                </th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{data.cotizaciones[id_empresa].sucursal}}</th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6"  >{{data.cotizaciones[id_empresa].direccion}}</th>
                                            </tr>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 20%;">Descripción</th>
                                                <th style="width: 6%;">Unidad</th>
                                                <th style="width: 6%;">Cantidad Solicitada</th>
                                                <th style="width: 6%;">Cantidad Asignada Previamente</th>
                                                <th style="width: 6%;">Cantidad Pendiente Asignar</th>
                                             
                                                <th class="bg-gray-light ">Precio Unitario</th>
                                                <th class="bg-gray-light">% Descuento</th>
                                                <th class="bg-gray-light ">Precio Total</th>
                                                <th class="bg-gray-light">Moneda</th>
                                                <th class="bg-gray-light ">Precio Total Moneda Conversión</th>
                                                <th class="bg-gray-light th_money_input">Cantidad Asignada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in data.items" v-if="item.item_pendiente">
                                                <td>{{ i+1}}</td>
                                                <td :title="item.descripcion">{{item.descripcion_corta}}</td>
                                                <td>{{item.unidad}}</td>
                                                <td>{{item.cantidad_solicitada}}</td>
                                                <td>{{item.cantidad_asignada}}</td>
                                                <td>{{item.cantidad_disponible}}</td>
                                                <td style="text-align: right" v-if="data.cotizaciones[id_empresa].partidas[i]">{{data.cotizaciones[id_empresa].partidas[i].precio_unitario_format}}</td><td v-else></td>
                                                <td v-if="data.cotizaciones[id_empresa].partidas[i]">{{data.cotizaciones[id_empresa].partidas[i].descuento}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.cotizaciones[id_empresa].partidas[i]">$ {{data.cotizaciones[id_empresa].partidas[i].importe}}</td><td v-else></td>
                                                <td v-if="data.cotizaciones[id_empresa].partidas[i]">{{data.cotizaciones[id_empresa].partidas[i].moneda}}</td><td v-else></td>
                                                <td style="text-align: right" v-if="data.cotizaciones[id_empresa].partidas[i]">$ {{data.cotizaciones[id_empresa].partidas[i].importe_moneda_conversion}}</td><td v-else></td>
                                                <td>
                                                    <span  v-if="data.cotizaciones[id_empresa].partidas[i]">
                                                        <input v-on:change="recalcular(i)"
                                                            type="number"
                                                            :disabled="item.cantidad_disponible == 0 && data.cotizaciones[id_empresa].partidas[i].cantidad_asignada == ''"
                                                            
                                                            class="form-control"
                                                            :name="`cantidad_asignada[${item.id_material}]`"
                                                            data-vv-as="Cantidad Asignada"
                                                            v-model="data.cotizaciones[id_empresa].partidas[i].cantidad_asignada"
                                                            v-validate="{max_value:item.cantidad_base, min_value:0}"
                                                            :class="{'is-invalid': errors.has(`cantidad_asignada[${item.id_material}]`)}"
                                                            id="cantidad_asignada">
                                                        <div class="invalid-feedback" v-show="errors.has(`cantidad_asignada[${item.id_material}]`)">{{ errors.first(`cantidad_asignada[${item.id_material}]`) }}</div>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
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
            solicitudes:[],
            data:null,
            id_solicitud:'',
            id_empresa:'',
        }
    },
    mounted() {
        this.getSolicitudes();
    },
    computed: {
        
    },
    methods: {
        getSolicitudes(){
            this.cargando = true;
            this.solicitudes = [];
            this.data = null;
            return this.$store.dispatch('compras/solicitud-compra/index', {
                params: {
                    scope: ['cotizacion'],
                    limit: 50,
                    order: 'DESC',
                    sort: 'numero_folio'
                }
            })
            .then(data => {
                this.solicitudes = data.data;
                this.cargando = false;
            })

        },
        getCotizaciones(id){
            this.cargando = true;
            this.data = null;
            return this.$store.dispatch('compras/solicitud-compra/getCotizaciones', {
                id: id,
                params: {}
            })
            .then(data => {
                this.id_empresa = Object.keys(data.cotizaciones)[0];
                this.data = data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        recalcular(i){
            console.log('entro');
            let asignadas = 0.0;

            Object.values(this.data.cotizaciones).forEach(partida =>{
                if(partida.partidas[i] && partida.partidas[i].cantidad_asignada !== ''){
                    asignadas = +asignadas + +partida.partidas[i].cantidad_asignada;
                }                   
            });

            if(asignadas > this.data.items[i].cantidad_base){
                if(this.data.items[i].cantidad_disponible === 0){
                    swal('¡Aviso!', 'El insumo ya no tiene cantidad pendiente por asignar', 'warning');
                }else{
                    swal('¡Aviso!', 'La cantidad asignada es mayor a la pendiente por asignar.', 'warning');
                }
                asignadas = +asignadas - +this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada;
                this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada = '';
            }else{
                let p_unitario = 0;
                this.data.cotizaciones[this.id_empresa].partidas[i].descuento > 0?
                    p_unitario = this.data.cotizaciones[this.id_empresa].partidas[i].precio_unitario - (this.data.cotizaciones[this.id_empresa].partidas[i].precio_unitario * (this.data.cotizaciones[this.id_empresa].partidas[i].descuento / 100))
                    :p_unitario =this.data.cotizaciones[this.id_empresa].partidas[i].precio_unitario;
                let c_asignada =this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada !== ''?this.data.cotizaciones[this.id_empresa].partidas[i].cantidad_asignada:0;
                this.data.cotizaciones[this.id_empresa].partidas[i].importe = parseFloat(p_unitario * c_asignada).formatMoney(2);
                this.data.cotizaciones[this.id_empresa].partidas[i].importe_moneda_conversion = parseFloat((p_unitario * c_asignada) * this.data.cotizaciones[this.id_empresa].partidas[i].tipo_cambio).formatMoney(2);
     
            }
            // this.data.items[i].cantidad_disponible = this.data.items[i].cantidad_base;
            this.data.items[i].cantidad_disponible = parseFloat(this.data.items[i].cantidad_base - asignadas).toFixed(4);
            console.log(this.data.items[i].cantidad_disponible);
        },
    },
    watch:{
        id_solicitud(value){
            if(value != ''){
                this.getCotizaciones(value);
            }
        }
    }
}
</script>
<style>

</style>