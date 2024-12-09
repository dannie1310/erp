<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'comprobacion-gasto-show', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link v-if="value.registrar" :to="{ name: 'comprobacion-gastos-relacion', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Generar">
            <i class="fa fa-plus"></i>
        </router-link>
        <router-link v-if="value.edit" :to="{ name: 'comprobacion-gasto-edit', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <button v-if="value.cerrar" @click="cerrar" type="button" class="btn btn-sm btn-outline-warning" title="Cerrar">
            <i class="fa fa-lock"></i>
        </button>
        <button v-if="value.abrir" @click="abrir" type="button" class="btn btn-sm btn-outline-info" title="Abrir">
            <i class="fa fa-unlock"></i>
        </button>
        <router-link :to="{ name: 'comprobacion-gasto-delete', params: {id: value.id}}" v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fa fa-trash"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "solicitud-comprobacion-gastos-action-buttons",
        props: ['value'],
        methods: {
            cerrar() {
                return this.$store.dispatch('controlRecursos/comprobacion/close', {
                    id: this.value.id
                })
                    .then(data => {
                        this.$store.commit('controlRecursos/comprobacion/UPDATE_COMPROBACION', data);
                    })
            },
            abrir() {
                return this.$store.dispatch('controlRecursos/comprobacion/open', {
                    id: this.value.id
                })
                    .then(data => {
                        this.find();
                    })
            },
            find() {
                return this.$store.dispatch('controlRecursos/comprobacion/find', {
                    id: this.value.id_comprobacion,
                    params:{include: []}
                }).then(data => {
                    this.$store.commit('controlRecursos/comprobacion/UPDATE_COMPROBACION', data);
                })
            },
        }
    }
</script>
