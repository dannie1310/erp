<template>
    <span>
        <div class="row">
            <div class="col-12">
                <button @click="create" v-if="$root.can('iniciar_expediente_proveedor',true)" class="btn btn-app btn-info float-right" :disabled="cargando">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-folder-plus" v-else></i>
                    Iniciar Expediente
                </button>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "padron-proveedores-index",
        data() {
            return {
                cargando: false
            }
        },
        methods: {
            create() {
                this.$router.push({name: 'proveedores-iniciar-expediente'});
            },
        },
    }
</script>
