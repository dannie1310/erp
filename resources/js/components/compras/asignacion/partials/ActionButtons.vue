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
        data() {
            return {
                cargando: false,
            }
        },
        methods:{
            descargaLayout() {
                console.log('Descarga de Layouts');
                return this.$store.dispatch('compras/asignacion/descargaLayout',{id: 5})
                    .then(() => {
                        this.$emit('success')

                    })
            },
        }
    }

</script>
