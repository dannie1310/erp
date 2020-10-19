<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'estimacion-show', params: {id: value.id}}" v-if="$root.can('consultar_estimacion_subcontrato')" type="button" class="btn btn-sm btn-primary" title="Ver" target="_blank">
                <i style="width:40px;" >{{value.numero_folio}}</i>
            </router-link>
            <PDF v-bind:id="value.id"></PDF>
            <FormatoOrdenPago v-bind:id="value.id"></FormatoOrdenPago>
            <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'"></ModalArchivos>
        </div>


    </span>
</template>

<script>
    import PDF from '../FormatoEstimacion';
    import FormatoOrdenPago from "../formato-orden-pago/FormatoOrdenPago";
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "action-buttons",
        components: {FormatoOrdenPago, PDF, ModalArchivos},
        props: ['value'],

        mounted() {

        },

    }
</script>
