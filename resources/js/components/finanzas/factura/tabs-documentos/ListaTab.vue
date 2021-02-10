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
                            <th class="bg-gray-light">Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.lista">
                            <td>{{item.referencia}}</td>
                            <td>{{item.folio}}</td>
                            <td>$ {{item.fecha}}</td>
                            <td>$ {{item.importe_total}}</td>
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
    name: "revision-lista-tab",
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