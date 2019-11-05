<template>
    <div class="btn-group">
        <button @click="descargaLayout" type="button"
                class="btn btn-sm btn-outline-success" title="Descargar Layout">
            <i class="fa fa-download"></i>
        </button>
        <PDF></PDF>
    </div>
</template>

<script>
    import PDF from "../FormatoAsignacion";
    export default {
        name: "action-buttons",
        components: {PDF},
        props: ['value'],
        data() {
            return {
                cargando: false,
            }
        },
        methods:{
            descargaLayout() {
                return this.$store.dispatch('almacenes/inventario-fisico/descargaLayout', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')

                    })
            },
        }
    }

</script>
