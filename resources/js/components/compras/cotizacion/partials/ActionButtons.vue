<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <DescargarLayout v-if="$root.can('descargar_layout_cotizacion_compra')" v-bind:id="value.id"></DescargarLayout>
        <button @click="edit" v-if="$root.can('editar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
    </div>
</template>
<script>
import Show from '../Show';
import DescargarLayout from '../DescargaLayout';
    export default {
        name: "solicitud-compra-buttons",
        components: {Show, DescargarLayout},
        props: ['value'],

        methods: {
            edit()
            {
                this.$router.push({ name: 'cotizacion-edit', params: {id: this.value.id}});
            }
        }
    }
</script>

<style scoped>

</style>
