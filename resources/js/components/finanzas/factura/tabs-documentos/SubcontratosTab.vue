<template>
    <span>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gray-light">Transacción</th>
                            <th class="bg-gray-light">Subcontrato</th>
                            <th class="bg-gray-light">Desde</th>
                            <th class="bg-gray-light">Hasta</th>
                            <th class="bg-gray-light">Monto Original</th>
                            <th class="bg-gray-light">Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody v-if="items">
                        <tr v-for="item in items.subcontratos">
                            <td>{{item.folio_revision_format}}</td>
                            <td>{{item.referencia_revision}}</td>
                            <td>{{item.fecha_inicial}}</td>
                            <td>{{item.fecha_final}}</td>
                            <td>{{item.monto_revision_format}}</td>
                            <td>{{item.monto_revision_format}}</td>
                            <td><input type="checkbox" id="seguir" :value="item.seleccionado"  v-model="item.seleccionado"   ></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "revision-subcontratos-tab",
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