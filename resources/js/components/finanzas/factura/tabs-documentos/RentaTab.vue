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
                            <th class="bg-gray-light">Importe Pesos</th>
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
                            <td>$ {{item.importe_total}}</td>
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
    props: ['items'],
    data() {
        return {
        }
    },
    methods: {},
    computed:{
        // items(){
        //     return this.$store.getters['finanzas/factura/items_revision'];
        // }
    },
}
</script>

<style>

</style>