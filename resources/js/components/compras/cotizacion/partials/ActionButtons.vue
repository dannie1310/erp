<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-bind:show="value.show"></Show>
        <CargaLayout v-on:back="layout" v-bind:id="value.id"></CargaLayout>
        <button @click="edit" v-if="$root.can('editar_cotizacion_compra')" type="button" class="btn btn-sm btn-outline-info" title="Editar">
                <i class="fa fa-pencil"></i>
        </button>
    </div>
</template>
<script>
import Show from '../Show';
import CargaLayout from '../CargaLayout';
    export default {
        name: "cotizacion-buttons",
        components: {Show, CargaLayout},
        props: ['value'],
        data()
        {
            return {
                xls: null
            }
        },

        methods: {
            edit()
            {
                this.$router.push({ name: 'cotizacion-edit', params: {id: this.value.id, xls: this.xls}});
            },
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
