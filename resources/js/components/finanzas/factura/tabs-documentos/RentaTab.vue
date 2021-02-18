<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Equipo</th>
                            <th class="bg-gray-light">Número Serie</th>
                            <th class="bg-gray-light">Importe Total</th>
                            <th class="bg-gray-light">Renta</th>
                            <th class="bg-gray-light">Unidad</th>
                            <th class="bg-gray-light" v-if="id_moneda == 2">Importe Dolares</th>
                            <th class="bg-gray-light" v-else-if="id_moneda == 3">Importe Euros</th>
                            <th class="bg-gray-light" v-else>Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.renta">
                            <td>{{item.equipo}}</td>
                            <td>{{item.numero_serie}}</td>
                            <td>$ {{item.importe_total}}</td>
                            <td>{{item.rentas}}</td>
                            <td>{{item.unidad}}</td>
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
    name: "revision-renta-tab",
    props: ['items', 'id_moneda', 'cambios'],
    data() {
        return {
        }
    },
    methods: {
        getMontoMoneda(item){
            if(parseInt(this.id_moneda) === parseInt(item.id_moneda)){
                return item.importe_total;
            }
            else{
                return parseFloat(item.importe_total_sf / this.cambios[this.id_moneda]).formatMoney(2);
            }
           
        },
    },
    computed:{
    },
}
</script>

<style>

</style>