<template>
    <span>
        <div class="row">
            <div class="col-12"  :disabled="cargando">
                <button  @click="carga_masiva" title="Cargar solicitud de cambio por layout" class="btn btn-app btn-info float-right" v-if="$root.can('editar_poliza')" >
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-upload" v-else></i>
                    Registrar
                </button>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "index-solicitud-edicion-poliza",
        components: { },
        data() {
            return {
                cargando: false,
            }
        },
        methods: {
            carga_masiva() {
                this.$router.push({name: 'solicitud-edicion-carga-masiva'});
            },
        }
    }
</script>

<style scoped>

</style>