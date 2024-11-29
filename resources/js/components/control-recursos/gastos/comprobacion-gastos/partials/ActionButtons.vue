<template>
    <div class="btn-group">
        <router-link v-if="value.registrar" :to="{ name: 'comprobacion-gastos-relacion', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Generar">
            <i class="fa fa-plus"></i>
        </router-link>
        <router-link v-if="value.edit" :to="{ name: 'comprobacion-gasto-edit', params: {id: value.id_comprobacion}}" type="button" class="btn btn-sm btn-outline-info" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
    </div>
</template>

<script>
    export default {
        name: "solicitud-comprobacion-gastos-action-buttons",
        props: ['value'],
        methods: {

        }
    }
</script>
