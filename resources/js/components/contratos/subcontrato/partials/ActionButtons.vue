<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show" v-if="$root.can('consultar_subcontrato')"></Show>
        <router-link  :to="{ name: 'subcontrato-edit', params: {id: value.id}}" v-if="$root.can('editar_subcontrato')" type="button" class="btn btn-sm btn-outline-success" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
         
        <Relaciones v-bind:transaccion="value.transaccion"/>
        <router-link  :to="{ name: 'subcontrato-documentos', params: {id: value.id}}" v-if="$root.can('consultar_subcontrato') && $root.can('consultar_archivos_transaccion')" type="button" class="btn btn-sm btn-outline-primary" title="Ver Documentos">
            <i class="fa fa-folder-open"></i>
        </router-link>
    </div>
</template>

<script>
    import Show from '../Show'
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "ActionButtons",
        components: {Show, Relaciones},
        props: ['value'],
    }
</script>
