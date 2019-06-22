<template>
    <span>
        <button @click="init" type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Ver">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-eye" v-else></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "cambiar-area-subcontratante"
    }
</script>

<style scoped>

</style>