<template>
    <span>
         <div class="col-12" v-if="Object.keys(asignacion).length > 0">
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
                                            <tr v-for="(item, i) in asignacion.partidas.data" v-if="item.item_pendiente">
                                                <td>{{ i+1}}</td>
                                                <td>{{ item.item_solicitud.material.descripcion}}</td>
                                                <td>{{ item.otem_solicitud.unidad}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                                <td>{{ i+1}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                        include: ['solicitud_compra','partidas.item_solicitud','partidas.cotizacion.empresa','partidas.cotizacion.sucursal']
                    }
                }).then(data => {
                    this.asignacion = data;
                    this.cotizacion = data.partidas.data[0].cotizacion;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
    },

}
</script>

<style>

</style>