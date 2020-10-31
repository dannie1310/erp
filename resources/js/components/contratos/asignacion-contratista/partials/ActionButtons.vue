<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"></Show>
        <PDF v-bind:id="value.id" />
        <router-link  :to="{ name: 'asignacion-proveedor-delete', params: {id: value.id}}" v-if="value.eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>

<script>
   import Show from '../Show';
   import PDF from '../FormatoTablaComparativa'
    export default {
        name: "action-buttons",
        components: {Show, PDF},
        props: ['value'],
        methods: {
        }
    }
</script>
