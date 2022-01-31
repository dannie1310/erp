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
            <cargar-layout v-bind:id="value.id" v-bind:base="value.base" />
            <router-link  :to="{ name: 'solicitud-autorizacion-avance-edit', params: {id: value.id, base: value.base}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Editar">
                <i class="fa fa-pencil"></i>Ir al Formulario
            </router-link>
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
        descargar() {
            this.cargando = true;
            return this.$store.dispatch('portalProveedor/solicitud-autorizacion-avance/descargaEdicionLayout', {id: this.value.id, base: atob(this.value.base) })
                .then(() => {
                    this.$emit('success')
                    this.cargando = false;
                })
        }
    }
}
</script>

<style scoped>
.dropdown-menu i{
    color: #529b5e !important;
}
</style>
