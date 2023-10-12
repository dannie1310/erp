<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'relacion-gasto-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-if="value.edit" :to="{ name: 'relacion-gasto-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <button v-if="value.cerrar" @click="cerrar" type="button" class="btn btn-sm btn-outline-warning" title="Cerrar">
            <i class="fa fa-lock"></i>
        </button>
        <button v-if="value.abrir" @click="abrir" type="button" class="btn btn-sm btn-outline-info" title="Abrir">
            <i class="fa fa-unlock"></i>
        </button>
    </div>
</template>

<script>
    export default {
        name: "relacion-gastos-action-buttons",
        components: {},
        props: ['value'],
        methods: {
            cerrar() {
                return this.$store.dispatch('controlRecursos/relacion-gasto/close', {
                    id: this.value.id
                })
                    .then(data => {
                        this.$store.commit('controlRecursos/relacion-gasto/UPDATE_RELACION', data);
                    })
            },
            abrir() {
                return this.$store.dispatch('controlRecursos/relacion-gasto/open', {
                    id: this.value.id
                })
                    .then(data => {
                        this.$store.commit('controlRecursos/relacion-gasto/UPDATE_RELACION', data);
                    })
            },
        }
    }
</script>
