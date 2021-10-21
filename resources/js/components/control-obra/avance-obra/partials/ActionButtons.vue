<template>
    <div class="btn-group">
       <router-link  :to="{ name: 'avance-obra-show', params: {id: value.id}}" v-if="$root.can('consultar_avance_obra')" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
           <i class="fa fa-eye"></i>
       </router-link>
    </div>
</template>
<script>
    export default {
        name: "avance-obra-buttons",
        components: {},
        props: ['value'],
        methods: {}
    }
</script>

<style scoped>

</style>
