<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'asignacion-contratista-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar Asignación">
            <i class="fa fa-eye"></i>
        </router-link>
        <PDF v-bind:id="value.id" />
        <router-link  :to="{ name: 'asignacion-contratista-delete', params: {id: value.id}}" v-if="value.eliminar" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar">
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
