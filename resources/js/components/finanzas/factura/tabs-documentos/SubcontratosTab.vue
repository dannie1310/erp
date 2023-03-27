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
                            <th class="bg-gray-light" v-if="id_moneda == 2">Importe Dolares</th>
                            <th class="bg-gray-light" v-else-if="id_moneda == 3">Importe Euros</th>
                            <th class="bg-gray-light" v-else>Importe Pesos</th>
                            <th class="bg-gray-light"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items.subcontratos">
                            <td>{{item.folio_revision_format}}</td>
                            <td>{{item.referencia_revision}}</td>
                            <td>{{item.fecha_inicial}}</td>
                            <td>{{item.fecha_final}}</td>
                            <td>{{item.monto_revision_format}}</td>
                            <td>$ {{parseFloat(getMontoMoneda(item)).formatMoney(2)}}</td>
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
            if(parseInt(item.tipo_transaccion) === 52){
                let importe = item.monto - item.impuesto;
                return '$ ' + parseFloat(importe * this.cambios[item.id_moneda]).formatMoney(2);
            }else if(parseInt(item.tipo_transaccion) === 51){
                return item.monto_revision * this.cambios[item.id_moneda];;
            } else if(parseInt(item.tipo_transaccion) === 53){
                return item.monto_revision_format
            }
        },
    },
    computed:{
    },
}
</script>

<style>

</style>
