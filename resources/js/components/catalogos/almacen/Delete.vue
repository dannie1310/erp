<template>
    <span>
        <button @click="eliminar" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "almacen-delete",
        props: ['id'],
        methods: {
            eliminar() {
                this.cargando = true;
                return this.$store.dispatch('cadeco/almacen/eliminar', {
                    id: this.id
                }).then(data => {
                    this.$store.commit('cadeco/almacen/DELETE_ALMACEN', data);
                })
            },
        }
    }
</script>

<style scoped>

</style>
