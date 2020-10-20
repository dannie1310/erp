<template>
    <div class="btn-group">
        <Consulta  v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" v-if="$root.can('consultar_cotizacion_compra')"/>
        <PDF v-bind:id="value.id" v-if="$root.can('consultar_cotizacion_compra')" />
        <ModalArchivos v-bind:id="value.id" v-bind:url="'/sao/modal/lista_archivos/{id}'" v-if="$root.can('consultar_cotizacion_compra')  && $root.can('consultar_archivos_transaccion')"></ModalArchivos>
    </div>
</template>
<script>

    import PDF from '../FormatoTablaComparativa';
    import Consulta from '../ShowModal';
    import ModalArchivos from "../../../globals/archivos/Modal";
    export default {
        name: "cotizacion-buttons",
        components: {Consulta, PDF, ModalArchivos},
        props: ['value'],
        data()
        {
            return {
                xls: null
            }
        },

        methods: {
            layout(dat)
            {
                this.xls = (dat) ? dat : null;
                if(this.xls)
                {
                    this.edit();
                }
            }
        }
    }
</script>

<style scoped>

</style>
