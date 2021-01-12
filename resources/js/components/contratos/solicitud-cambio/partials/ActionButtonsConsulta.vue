<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'solicitud-cambio-show', params: {id: value.id}}" v-if="$root.can('consultar_solicitud_cambio_subcontrato')" type="button" class="btn btn-sm btn-primary" title="Ver" target="_blank">
                <i style="width:40px;" >{{value.numero_folio}}</i>
            </router-link>
            <PDF v-bind:id="value.id" v-if="$root.can('consultar_solicitud_cambio_subcontrato')"></PDF>
            <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_solicitud_cambio_subcontrato') && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
        </div>


    </span>
</template>

<script>
    import PDF from '../Formato';
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "action-buttons",
        components: {PDF, ModalArchivos},
        props: ['value'],

        mounted() {

        },

    }
</script>
