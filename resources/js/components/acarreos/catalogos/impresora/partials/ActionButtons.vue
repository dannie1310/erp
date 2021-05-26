<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" />
        <Edit v-bind:id="value.id" v-if="value.edit" />
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check-square"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar" />
    </div>
</template>

<script>
    import Show from "../Show"
    import Desactivar from "../Desactivar";
    import Edit from "../Edit";
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: { Show, Desactivar, Edit },
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/impresora/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/impresora/UPDATE_IMPRESORA', data);
                    })
            },
        }
    }
</script>

<style scoped>

</style>
