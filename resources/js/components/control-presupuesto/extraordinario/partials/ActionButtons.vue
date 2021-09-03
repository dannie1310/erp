<template>
    <div class="btn-group">
        <button @click="show" type="button" class="btn btn-sm btn-outline-secondary" v-if="$root.can('consultar_variacion_volumen')"  title="Consultar"><i class="fa fa-eye"></i></button>
        <router-link  :to="{ name: 'variacion-volumen-autorizar', params: {id: value.id}}" v-if="($root.can('autorizar_variacion_volumen') || $root.can('rechazar_variacion_volumen')) && value.estado == 1" type="button" class="btn btn-sm btn-outline-danger" title="Aplicar">
            <i class="fa fa-thumbs-o-up"></i>
        </router-link>
        <PdfVariacion v-bind:id="value.id"></PdfVariacion>
    </div>
</template>

<script>
    import PdfVariacion from './FormatoVariacionVolumen';
    export default {
        name: "action-buttons",
        components: {PdfVariacion},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'variacion-volumen-show', params: {id: this.value.id}});
            },
        },
        computed: {
        },
        mounted() {
        }
    }
</script>
