<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button @click="edit" v-if="value.edit && value.estatus" type="button" class="btn btn-sm btn-outline-info" title="Editar"><i class="fa fa-pencil"></i></button>
        <button @click="historico"  v-if="value.historico" type="button" class="btn btn-sm btn-outline-success" title="Histórico"><i class="fa fa-clock-o"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        props: ['value'],
        methods: {
            historico() {
                this.$router.push({name: 'poliza-historico', params: {id: this.value.id}});
            },
            show() {
                this.$router.push({name: 'poliza-show', params: {id: this.value.id}});
            },
            edit() {
                this.$router.push({name: 'poliza-edit', params: {id: this.value.id}});
            }
        }
    }
</script>