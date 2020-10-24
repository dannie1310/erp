<template>
    <div class="btn-group">
        <asignar-concepto v-bind:id="value.id" v-if="value.concepto" />
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check-square"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar"></Desactivar>
    </div>
</template>

<script>
    import AsignarConcepto from "../AsignarConcepto";
    import Desactivar from "../Desactivar";
    export default {
        name: "ActionButtons",
        components: {AsignarConcepto, Desactivar},
        props: ['value'],
        methods: {
            activar() {
                return this.$store.dispatch('acarreos/tiro/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/tiro/UPDATE_TIRO', data);
                    })
            },

        }
    }
</script>

<style scoped>

</style>
