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
                            <th class="bg-gray-light">Importe Pesos</th>
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
                            <td>$ {{item.monto}}</td>
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
    props: ['items'],
    data() {
        return {

        }
    },
    methods: {
        actualizar(item){
            // item.seleccionado = true;
            // console.log(item);
            this.items.pendientes.forEach(pendiente => {
                pendiente['seleccionado'] = item.seleccionado;
            });
            
            // this.$store.commit('finanzas/factura/UPDATE_ITEM_PENDIENTE', item);
            // 
        },
    },
    computed:{
        // items(){
        //     return this.$store.getters['finanzas/factura/items_revision'];
        // }
    },
}
</script>

<style>

</style>