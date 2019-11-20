<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-secondary" title="Show" v-if="$root.can('registrar_marbetes_manualmente')">
            <i class="fa fa-eye"></i>
        </button>
        
    </span>
</template>

<script>
    export default {
        name: "requisicion-show",
        props: ['id'],
        data() {
            return {
                requisicion: []
            }
        },
        methods: {
            find() {
                this.$store.commit('compras/requisicion/SET_REQUISICION', null);
                return this.$store.dispatch('compras/requisicion/find', {
                    id: this.id,
                    params: {include: ['partidas']}
                }).then(data => {
                    this.$store.commit('compras/requisicion/SET_REQUISICION', data);
                    this.ajustes = data;
                    $(this.$refs.modal).modal('show');
                })
            }
        }
    }
</script>

<style scoped>

</style>