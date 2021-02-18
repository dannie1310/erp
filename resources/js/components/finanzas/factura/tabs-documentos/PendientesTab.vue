<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Remisión</th>
                            <th class="bg-gray-light">Fecha</th>
                            <th class="bg-gray-light">Insumo</th>
                            <th class="bg-gray-light">Cantidad</th>
                            <th class="bg-gray-light">Precio</th>
                            <th class="bg-gray-light">Monto Original</th>
                            <th class="bg-gray-light">Anticipo</th>
                            <th class="bg-gray-light">Monto</th>
                            <th class="bg-gray-light" v-if="id_moneda == 2">Importe Dolares</th>
                            <th class="bg-gray-light" v-else-if="id_moneda == 3">Importe Euros</th>
                            <th class="bg-gray-light" v-else>Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.pendientes">
                            <td>{{item.remision}}</td>
                            <td>{{item.fecha}}</td>
                            <td>{{item.insumo}}</td>
                            <td>{{item.cantidad}}</td>
                            <td>$ {{item.precio}}</td>
                            <td>$ {{item.monto_original}}</td>
                            <td>{{item.anticipo}}</td>
                            <td>$ {{item.monto}}</td>
                            <td>$ {{getMontoMoneda(item)}}</td>
                            <td><input type="checkbox" id="seguir" :value="item.seleccionado" v-model="item.seleccionado"  ></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "revision-pendientes-tab",
    props: ['items', 'id_moneda', 'cambios'],
    data() {
        return {

        }
    },
    methods: {
        actualizar(item){
            this.items.pendientes.forEach(pendiente => {
                pendiente['seleccionado'] = item.seleccionado;
            });
        },
        getMontoMoneda(item){
            if(parseInt(this.id_moneda) === parseInt(item.id_moneda)){
                return item.monto;
            }
            else{
                return parseFloat(item.monto_sf / this.cambios[this.id_moneda]).formatMoney(2);
            }
           
        },
    },
    computed:{
    },
}
</script>

<style>

</style>