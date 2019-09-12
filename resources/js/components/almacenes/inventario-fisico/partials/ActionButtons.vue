<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF" v-if="value.show" @click="pdf_marbetes(value.id)"><i class="fa fa-file-pdf-o"></i> </button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value'],
        methods:{
            pdf_marbetes(id) {
                return this.$store.dispatch('almacenes/inventario-fisico/pdf_marbetes', {id:id})
                    .then(data => {
                    }).finally( ()=>{
                    });
            },
        }
    }

</script>
