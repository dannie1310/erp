<template>
    <div class="btn-group">
        <Show v-if="$root.can('editar_presupuesto_contratista')" v-bind:id="value.id"></Show>
        <button @click="edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
    </div>
</template>
<script>
import Show from '../Show';
    export default {
        name: "presupuesto-buttons",
        components: {Show},
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