<template>
    <span>
        <div class="row">
            <div class="col-12">
                
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(asignaciones).length > 0">
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Asignación: </b></label>
                                    {{asignaciones.folio_asignacion_format}}
                                </div>
                            </div>
                            <div class="col-md-5" >
                                <div class="form-group">
                                    <label><b>Usuario Registro Asignación: </b></label>
                                    {{asignaciones.usuario.nombre}}
                                </div>
                            </div>
                            
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Estado: </b></label>
                                    {{asignaciones.estado_format}}
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha de Registro: </b></label>
                                    {{asignaciones.fecha_format}}
                                </div>
                            </div>

                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    {{asignaciones.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-7" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    {{asignaciones.observaciones}}
                                </div>
                            </div>
                            
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha y Hora de Registro: </b></label>
                                    {{asignaciones.fecha_registro}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="Object.keys(asignaciones).length > 0">
                
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" @click="generarOC" >Generar Orden Compra</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <br/>
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="4" rowspan="4" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >
                                                    <select
                                                        type="text"
                                                        name="id_transaccion"
                                                        data-vv-as="Razón Social"
                                                        class="form-control"
                                                        id="id_transaccion"
                                                        v-model="id_transaccion">
                                                        <option v-for="asignacion in asignaciones.data" :value="asignacion.id_transaccion">{{ asignacion.razon_social }}</option>
                                                    </select>
                                                </th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{asignaciones.data[id_transaccion].sucursal}}</th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6"  >{{asignaciones.data[id_transaccion].direccion}}</th>
                                            </tr>
                                            <tr>
                                                <th>#</th>
                                                <th style="width: 32%;">Descripción</th>
                                                <th style="width: 8%;">Unidad</th>
                                                <th style="width: 8%;">Cantidad Solicitada</th>
                                                
                                                <th class="bg-gray-light ">Precio Unitario</th>
                                                <th class="bg-gray-light">% Descuento</th>
                                                <th class="bg-gray-light ">Precio Total</th>
                                                <th class="bg-gray-light">Moneda</th>
                                                <th class="bg-gray-light ">Precio Total Moneda Conversión</th>
                                                <th class="bg-gray-light th_money_input">Cantidad Asignada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in asignaciones.data[id_transaccion].partidas" v-if="asignaciones.data[id_transaccion].partidas">
                                                <td>{{ i+1}}</td>
                                                <td>{{ item.descripcion}}</td>
                                                <td>{{ item.unidad}}</td>
                                                <td>{{ item.cantidad_solicitada}}</td>
                                                <td class="money">{{ item.precio_unitario}}</td>
                                                <td>{{ item.descuento}}</td>
                                                <td class="money">{{ item.precio_total }}</td>
                                                <td>{{ item.moneda}}</td>
                                                <td class="money">{{ item.precio_moneda_conv }}</td>
                                                <td>{{ item.cantidad_asignada}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
export default {
    name: "asignacion-proveedores-show",
    props: ['id'],
    data() {
        return {
            cargando: false,
            asignaciones:[],
            cotizacion:[],
            id_transaccion:'',
        }
    },
    mounted() {
        this.getAsignacion();
    },
    computed: {
        
    },
    methods: {
        asignacion(){
            this.cargando = true;
            this.asignacion = [];
            return this.$store.dispatch('compras/asignacion/find', {
                    id: this.id,
                    params:{
                        include: ['solicitud_compra','partidas.item_solicitud', 'partidas.cotizacion_partida','partidas.cotizacion.empresa','partidas.cotizacion.sucursal']
                    }
                }).then(data => {
                    this.asignacion = data;
                    this.cotizacion = data.partidas.data[0].cotizacion;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        getAsignacion(){
            this.cargando = true;
            this.asignacion = [];
            return this.$store.dispatch('compras/asignacion/getAsignacion', {
                    id: this.id,
                    params:{
                        
                    }
                }).then(data => {
                    this.asignaciones = data;
                    this.id_transaccion = Object.keys(data.data)[0];
                }).finally(()=>{
                    this.cargando = false;
                })
        },
        generarOC(){
            return this.$store.dispatch('compras/asignacion/generarOC', {
                    data: {
                        id: this.id
                    }
                }).then(data => {
                    this.$router.push({name: 'asignacion-proveedores'});
                }) .finally(() => {
                    this.descargando = false;
                })
        },
        precioTotal(index){
            let descuento = isNaN(this.asignacion.partidas.data[index].cotizacion_partida.descuento)?0:this.asignacion.partidas.data[index].cotizacion_partida.descuento;
            if(descuento === 0){
                return this.asignacion.partidas.data[index].cantidad * this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario;
            }
            let p_u_descuento = ((descuento/100) * this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario) - this.asignacion.partidas.data[index].cotizacion_partida.precio_unitario;
            return this.asignacion.partidas.data[index].cantidad * p_u_descuento
        },
        precioTotalMC(index){
            return this.precioTotal(index) * this.asignacion.partidas.data[index].cotizacion_partida.moneda.tipo_cambio;
        },

    },

}
</script>

<style>

</style>