<template>
    <div class="btn-group">
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check-square"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar"></Desactivar>
    </div>
</template>

<script>
    import Desactivar from "../Desactivar";
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: {Desactivar},
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/origen/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/origen/UPDATE_ORIGEN', data);
                    })
            },

        }
    }
</script>

<style scoped>

</style>
