<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Concepto</th>
                            <th class="bg-gray-light">Naturaleza</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.descuentos">
                            <td>{{item.concepto}}</td>
                            <td>{{item.naturaleza}}</td>
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
    name: "revision-descuentos-tab",
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