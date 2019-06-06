<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "distribuir-recurso-remesa-show",
        props: ['id'],
        methods: {
            find(id) {
                this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', null);
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/find', {
                    id: id,
                    params: { include: 'remesa_liberada.remesa.documento' }
                }).then(data => {
                    this.$store.commit('finanzas/distribuir-recurso-remesa/SET_DISTRIBUCION', data);
                    $(this.$refs.modal).modal('show')
                })
            }
        },
        computed: {
            distribucionRecurso() {
                return this.$store.getters['finanzas/distribuir-recurso-remesa/currentDistribucion']
            }
        }
    }
</script>

<style scoped>

</style>