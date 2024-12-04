<template>
    <div class="btn-group">
        <router-link v-if="value.registrar" :to="{ name: 'solicitud-comprobacion-gasto-create', params: {id: value.id}}" type="button" class="btn btn-sm btn-outline-info" title="Generar">
            <i class="fa fa-plus"></i>
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
