<template>
    <span>
         <button class="btn btn-sm btn-outline-info dropdown-toggle"
            type="button"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            data-boundary="window"
            aria-haspopup="true"
            aria-expanded="false">
            <span><i class="fa fa-pencil"></i></span>
        </button>
        <div class="dropdown-menu">
            <button @click="descargar" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Descargar LAyout" >
                <i class="fa fa-download" v-if="!cargando"></i>
                <i class="fa fa-spinner fa-spin" v-else></i>Descargar Layout
            </button>
            <!-- <button @click="cargar" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Cargar" >
                <i class="fa fa-upload"></i>Cargar Layout
            </button> -->
            <cargar-layout v-bind:id="value.id" />
            <button @click="editCondiciones" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Editar Condiciones" >
                <i class="fa fa-pencil"></i>Editar Condiciones
            </button>
            <button @click="edit" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Editar" >
                <i class="fa fa-pencil"></i>Ir a Formulario
            </button>
            
        </div>
    </span>
</template>

<script>
import CargarLayout from "../CargaLayoutEdit";
export default {
    name: "edit-drop-list",
        components: {CargarLayout},
        props: ['value'],
        data() {
            return {
                cargando: false,
            }
        },

        mounted() {
        },

        methods: {
            edit(){
                this.$router.push({ name:'estimacion-edit', params: {id: this.value.id}});
            },
            editCondiciones(){
                this.$router.push({ name:'estimacion-edit-condiciones', params: {id: this.value.id}});
            },
            descargar() {
                this.cargando = true;
                return this.$store.dispatch('contratos/estimacion/descargaLayoutEdicion', {id: this.value.id})
                .then(() => {
                    this.$emit('success')
                    this.cargando = false;
                })

            },
            cargar(){

            },
        }
}
</script>

<style scoped>
.dropdown-menu i{
    color: #529b5e !important;
}
</style>