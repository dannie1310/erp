<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Referencia</th>
                            <th class="bg-gray-light">Folio</th>
                            <th class="bg-gray-light">Fecha</th>
                            <th class="bg-gray-light">Importe</th>
                            <th class="bg-gray-light" v-if="id_moneda == 2">Importe Dolares</th>
                            <th class="bg-gray-light" v-else-if="id_moneda == 3">Importe Euros</th>
                            <th class="bg-gray-light" v-else>Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.lista">
                            <td>{{item.referencia}}</td>
                            <td>{{item.folio}}</td>
                            <td>$ {{item.fecha}}</td>
                            <td>$ {{item.importe_total}}</td>
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
    name: "revision-lista-tab",
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