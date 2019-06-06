<template>
    <div class="btn-group">
        <DistribuirRecursoRemesaShow v-if="value.show" v-bind:id="value.id" />
        <!--<DistribuirRecursoRemesaEdit v-if="value.edit" v-bind:id="value.id" />-->
        <!--<button @click="cancelar"  v-if="value.cancelar && value.estado === 0" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>-->
    </div>
</template>

<script>
    import DistribuirRecursoRemesaShow from "../Show";
    // import DistribuirRecursoRemesaEdit from "../Edit";
    export default {
        name: "action-buttons",
        components: {DistribuirRecursoRemesaShow},
        props: ['value'],
        methods: {
            cancelar() {

            },
            destroy() {

            },
            show() {
                this.$router.push({name: 'distribuir-recurso-remesa-show', params: {id: this.value.id}});
            }
        },
        mounted() {
        }
    }
</script>