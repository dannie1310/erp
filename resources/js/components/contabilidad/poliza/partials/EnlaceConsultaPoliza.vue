<template>
    <span>
        <router-link  :to="{ name: 'poliza-show', params: {id: id}}" v-if="$root.can('consultar_prepolizas_generadas')" target="_blank" style="text-decoration: underline; color: black">
             {{folio}}
        </router-link>
        <span v-else>
            {{folio}}
        </span>
    </span>
</template>

<script>
export default {
    name: "EnlaceConsultaPoliza",
    props: ['id','folio'],
}
</script>

<style scoped>

</style>
