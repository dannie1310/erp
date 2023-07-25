<template>
    <span>
        <router-link  :to="{ name: 'poliza-contpaq-show', params: {id: id, id_empresa:id_empresa}}" v-if="$root.can('consultar_prepolizas_generadas')" target="_blank" style="text-decoration: underline; color: black">
             {{folio}}
        </router-link>
        <span v-else>
            {{folio}}
        </span>
    </span>
</template>

<script>
export default {
    name: "EnlaceConsultaPolizaContpaq",
    props: ['id','id_empresa','folio'],
}
</script>

<style scoped>

</style>
