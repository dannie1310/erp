<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-proveedor-send', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.enviar" type="button" class="btn btn-sm btn-outline-success" title="Enviar">
            <i class="fa fa-send"></i>
        </router-link>
    </div>
</template>
<script>
    export default {
        name: "cotizacion-buttons",
        props: ['value'],
    }
</script>

<style scoped>

</style>
