<template>
    <span>
        <div class="btn-group">
            <router-link  :to="{ name: 'estimacion-show', params: {id: value.id}}" v-if="$root.can('consultar_estimacion_subcontrato')" type="button" class="btn btn-sm btn-primary" title="Ver" target="_blank">
                <i style="width:40px;" >{{value.numero_folio}}</i>
            </router-link>
            <PDF v-bind:id="value.id"></PDF>
            <FormatoOrdenPago v-bind:id="value.id"></FormatoOrdenPago>
        </div>


    </span>
</template>

<script>
    import PDF from '../FormatoEstimacion';
    import FormatoOrdenPago from "../formato-orden-pago/FormatoOrdenPago";
    export default {
        name: "action-buttons",
        components: {FormatoOrdenPago, PDF},
        props: ['value'],

        mounted() {

        },

    }
</script>
