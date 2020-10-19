<template>
    <span>
        <div class="btn-group">
            <FacturaShow v-if="value.show" v-bind:id="value.id" />
            <PDF v-bind:id="value.id" @click="value.id" ></PDF>
            <Eliminar v-if="value.borrar" v-bind:id="value.id" v-bind:pagina="value.pagina" />
            <Revertir v-if="value.revertir" v-bind:id="value.id" />
            <Relaciones v-bind:transaccion="value.transaccion"/>
            <router-link  :to="{ name: 'factura-documentos', params: {id: value.id}}" v-if="$root.can('consultar_factura')" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
                <i class="fa fa-folder-open"></i>
            </router-link>
        </div>
    </span>
</template>

<script>
    import PDF from '../FormatoCR';
    import FacturaShow from "../Show";
    import Eliminar from '../Eliminar';
    import Revertir from "../Revertir";
    import Relaciones from "../../../globals/ModalRelaciones";
    export default {
        name: "action-buttons",
        components: {Revertir, PDF, FacturaShow, Eliminar, Relaciones},
        props: ['value'],
    }
</script>

<style scoped>

</style>
