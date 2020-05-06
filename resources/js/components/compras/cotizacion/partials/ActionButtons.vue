<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <button @click="edit" v-if="$root.can('editar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
        <Delete v-bind:id="value.id" v-if="value.delete"/>
    </div>
</template>
<script>
import Show from '../Show';
import Delete from "../Delete";
    export default {
        name: "cotizacion-buttons",
        components: {Delete, Show},
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
