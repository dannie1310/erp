<template>
    <div class="btn-group">
        <button @click="show" type="button"
                class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <PDF></PDF>
        <Eliminar v-if="$root.can('eliminar_asignacion_proveedor')" v-bind:id="value.id"></Eliminar>
    </div>
</template>

<script>
    import PDF from "../FormatoAsignacion";
    import Eliminar from '../Delete';
    export default {
        name: "action-buttons",
        components: {PDF, Eliminar},
        props: ['value'],
        data() {
            return {
                cargando: false,
            }
        },
        methods:{
            show(){
                this.$router.push({name: 'asignacion-proveedores-show', params: { id: this.value.id }});
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
