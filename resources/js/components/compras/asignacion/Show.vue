<template>
    <span>
        <div class="row">
            <div class="col-12">
                
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(asignacion).length > 0">
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Asignación: </b></label>
                                    {{asignacion.folio_asignacion_format}}
                                </div>
                            </div>
                            <div class="col-md-5" >
                                <div class="form-group">
                                    <label><b>Usuario Registro Asignación: </b></label>
                                    {{asignacion.usuario.nombre}}
                                </div>
                            </div>
                            
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Estado: </b></label>
                                    {{asignacion.estado}}
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha de Registro: </b></label>
                                    {{asignacion.fecha_format}}
                                </div>
                            </div>

                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    {{asignacion.solicitud_compra.numero_folio_format}}
                                </div>
                            </div>
                            <div class="col-md-7" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    {{asignacion.solicitud_compra.observaciones}}
                                </div>
                            </div>
                            
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label><b>Fecha y Hora de Registro: </b></label>
                                    {{asignacion.solicitud_compra.fecha_registro}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="Object.keys(asignacion).length > 0">
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="4" rowspan="4" class="text-left"><h5></h5></th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{cotizacion.empresa.razon_social}} </th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6" >{{cotizacion.sucursal.descripcion}}</th>
                                            </tr>
                                            <tr class="bg-gray-light">
                                                <th colspan="6"  >{{cotizacion.sucursal.direccion}}</th>
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
                                            <tr v-for="(item, i) in asignacion.partidas.data" v-if="asignacion.partidas">
                                                <td>{{ i+1}}</td>
                                                <td>{{ item.item_solicitud.material.descripcion}}</td>
                                                <td>{{ item.item_solicitud.unidad}}</td>
                                                <td>{{ item.item_solicitud.cantidad}}</td>
                                                <td class="money">{{ item.cotizacion_partida.precio_unitario_format}}</td>
                                                <td>{{ item.cotizacion_partida.porcentaje_descuento}}</td>
                                                <td class="money">{{ '$' + parseFloat(precioTotal(i)).formatMoney(2) }}</td>
                                                <td>{{ item.cotizacion_partida.moneda.abreviatura}}</td>
                                                <td class="money">{{ '$' + parseFloat(precioTotalMC(i)).formatMoney(2) }}</td>
                                                <td>{{ item.cantidad_format}}</td>
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
            asignacion:[],
            cotizacion:[],
        }
    },
    mounted() {
        this.getAsignacion();
    },
    computed: {
        
    },
    methods: {
        getAsignacion(){
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