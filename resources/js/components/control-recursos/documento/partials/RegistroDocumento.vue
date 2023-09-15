<template>
    <span>
        <button v-if="$root.can('registrar_factura_recursos',true) || $root.can('registrar_documento_recursos',true)"
            :disabled="cargando"
                class="btn btn-app pull-right dropdown-toggle"
            type="button"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            data-boundary="window"
            aria-haspopup="true"
            aria-expanded="false">
            <span><i class="fa fa-plus"></i></span>
            Registrar
        </button>
        <div class="dropdown-menu">
            <button @click="load" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Cargar" >
                <i class="fa fa-file-invoice-dollar"></i>Registrar con CFDI
            </button>
            <button @click="registrar" type="button" class="btn btn-sm btn-outline-info dropdown-item" title="Editar" >
                <i class="fa fa-pencil"></i>Registrar Manualmente
            </button>
        </div>
    </span>
</template>

<script>
export default {
    name: "registro-documento",
    props: ['cargando'],
    data() {
        return {
            procesando:false,
            file_carga : null,
            file_carga_name : '',
            data: null,
        }
    },
    mounted(){
    },
    methods:{
        load() {
            this.$router.push({name: 'documento-create-cfdi'});
        },

        registrar(){
            this.$router.push({name: 'documento-create-manual'});
        },
    },

}
</script>

<style scoped>

</style>
