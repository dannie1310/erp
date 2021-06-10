<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'camion-show', params: {id: value.id}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'camion-edit', params: {id: value.id}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <button @click="activar" v-if="value.activar" type="button" class="btn btn-sm btn-outline-success" title="Activar">
            <i class="fa fa-check"></i>
        </button>
        <Desactivar v-bind:id="value.id" v-if="value.desactivar" />
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
                return this.$store.dispatch('acarreos/camion/activar', {
                    id: this.value.id,
                    params: {}})
                    .then((data) => {
                        this.$store.commit('acarreos/camion/UPDATE_CAMION', data);
                    })
            },
        }
    }
</script>

<style scoped>

</style>
