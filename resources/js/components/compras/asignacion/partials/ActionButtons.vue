<template>
    <div class="btn-group">
        <button @click="show" type="button"
                class="btn btn-sm btn-outline-default" title="Ver">
            <i class="fa fa-eye"></i>
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
            show(){
                this.$router.push({name: 'asignacion-proveedores-show'});
            },
            // descargaLayout() {
            //     console.log('Descarga de Layouts');
            //     return this.$store.dispatch('compras/asignacion/descargaLayout',{id: 5})
            //         .then(() => {
            //             this.$emit('success')

            //         })
            // },
        }
    }

</script>
