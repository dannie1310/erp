<template>
    <div class="btn-group">
        <router-link :to="{ name: 'registrar-pago', params: {id: this.value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Registrar">
            <i class="fa fa-plus"></i>
        </router-link>
        <router-link :to="{ name: 'pago-show', params: {id: this.value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value']
    }
</script>

<style scoped>

</style>
