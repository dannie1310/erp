<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <!--<DistribuirRecursoRemesaEdit v-if="value.edit" v-bind:id="value.id" />-->
        <button @click="cancelar" v-if="value.cancelar && (value.estado === 0 || value.estado === 1)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value'],
        methods: {
            cancelar() {
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/cancel', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            destroy() {

            },
            show() {
                this.$router.push({name: 'distribuir-recurso-remesa-show', params: {id: this.value.id}});
            }
        },
        mounted() {
        }
    }
</script>