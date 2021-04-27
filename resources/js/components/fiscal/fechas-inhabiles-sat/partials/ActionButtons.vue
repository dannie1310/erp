<template>
    <div class="btn-group">
        <button @click="destroy" v-if="value.eliminar" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fa fa-trash"></i></button>
    </div>
</template>

<script>
    export default {
        name: "ActionButtons",
        props: ['value'],
        methods: {
            destroy() {
                return this.$store.dispatch('finanzas/fecha-inhabil-sat/delete', this.value.id)
                    .then(() => {
                        this.$store.commit('finanzas/fecha-inhabil-sat/DELETE_FECHA', this.value.id);
                    })
            }
        }
    }
</script>

<style scoped>

</style>
