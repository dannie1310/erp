<template>
    <div class="btn-group">
        <Show v-bind:id="value.id"></Show>
        <router-link  :to="{ name: 'asignacion-proveedor-delete', params: {id: value.id}}" v-if="$root.can('eliminar_asignacion_contratista')" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>

<script>
   import Show from '../Show';
    export default {
        name: "action-buttons",
        components: {Show},
        props: ['value'],
        methods: {
        }
    }
</script>
