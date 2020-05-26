<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"></Show>
        <DescargaLayout v-if="$root.can('descargar_layout_presupuesto_contratista')" v-bind:id="value.id"></DescargaLayout>
        <Eliminar v-if="$root.can('eliminar_presupuesto_contratista')" v-bind:id="value.id"></Eliminar>
        <button v-if="$root.can('editar_presupuesto_contratista')" @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
    </div>
</template>
<script>
import Show from '../Show';
import Eliminar from '../Delete';
import DescargaLayout from '../DescargaLayout'
    export default {
        name: "presupuesto-buttons",
        components: {Show, Eliminar, DescargaLayout},
        props: ['value'],

        methods: {
            edit()
            {
                this.$router.push({ name: 'presupuesto-edit', params: {id: this.value.id}});                
            }
        }
    }
</script>

<style scoped>

</style>