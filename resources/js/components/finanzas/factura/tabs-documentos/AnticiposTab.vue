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
                            <th class="bg-gray-light">Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.anticipos">
                            <td>{{item.transaccion}}</td>
                            <td>{{item.fecha}}</td>
                            <td>{{item.descripcion_item}}</td>
                            <td>$ {{item.anticipo}}</td>
                            <td>$ {{item.anticipo}}</td>
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