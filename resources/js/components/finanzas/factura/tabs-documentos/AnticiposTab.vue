<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Transacción</th>
                            <th class="bg-gray-light">Fecha</th>
                            <th class="bg-gray-light">Descripción Item</th>
                            <th class="bg-gray-light">Monto Original</th>
                            <th class="bg-gray-light" v-if="id_moneda == 2">Importe Dolares</th>
                            <th class="bg-gray-light" v-else-if="id_moneda == 3">Importe Euros</th>
                            <th class="bg-gray-light" v-else>Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items.anticipos">
                            <td>{{item.transaccion}}</td>
                            <td>{{item.fecha}}</td>
                            <td>{{item.descripcion_item}}</td>
                            <td>$ {{item.anticipo}}</td>
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
    name: "revision-anticipo-tab",
    props: ['items', 'id_moneda', 'cambios'],
    data() {
        return {
        }
    },
    methods: {
        getMontoMoneda(item){
            if(parseInt(this.id_moneda) === parseInt(item.id_moneda)){
                return item.anticipo;
            }
            else{
                return parseFloat(item.anticipo_sf / this.cambios[this.id_moneda]).formatMoney(2);
            }
           
        },
    },
    computed:{
    },
}
</script>

<style>

</style>