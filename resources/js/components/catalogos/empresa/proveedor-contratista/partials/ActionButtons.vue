<template>
    <div class="btn-group">
        <button @click="find(value.id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value'],
        methods: {
            find(id) {
                this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', null);
                return this.$store.dispatch('cadeco/proveedor-contratista/find', {
                    id: id,
                    params: {include: ['suministrados.material', 'sucursales']}
                }).then(data => {
                    this.$store.commit('cadeco/proveedor-contratista/SET_PROVEEDOR_CONTRATISTA', data);
                })
            },
        },
    }
</script>

<style scoped>

</style>
