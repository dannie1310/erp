<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'extraordinario-show', params: {id: value.id}}" v-if="$root.can('consultar_extraordinario')" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'extraordinario-autorizar', params: {id: value.id}}" v-if="($root.can('autorizar_extraordinario') || $root.can('rechazar_extraordinario')) && value.estado == 1" type="button" class="btn btn-sm btn-outline-danger" title="Aplicar">
            <i class="fa fa-thumbs-o-up"></i>
        </router-link>
        <Pdf v-bind:id="value.id"></Pdf>
    </div>
</template>

<script>
    import Pdf from './Formato';
    export default {
        name: "action-buttons",
        components: {Pdf},
        props: ['value'],
        methods: {

        },
        computed: {
        },
        mounted() {
        }
    }
</script>
