<template>
    <div class="btn-group">
        <Show v-bind:id="value.id" v-if="value.show" />
        <Edit v-bind:id="value.id" v-if="value.edit" />
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar" />
    </div>
</template>

<script>
    import Desactivar from "../Desactivar";
    import Edit from "../Edit";
    import Show from "../Show"
    export default {
        name: "ActionButtons",
        props: ['value'],
        components: {Show, Edit, Desactivar},
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/operador/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/operador/UPDATE_OPERADOR', data);
                    })
            },

        }
    }
</script>

<style scoped>

</style>
