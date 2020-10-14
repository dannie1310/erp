<template>
    <div class="btn-group">
        <Consulta  v-bind:id="value.id" v-bind:numero_folio="value.numero_folio" />
        <PDF v-bind:id="value.id" />
    </div>
</template>
<script>

    import PDF from '../FormatoTablaComparativa';
    import Consulta from '../ShowModal';
    export default {
        name: "cotizacion-buttons",
        components: {Consulta, PDF},
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
