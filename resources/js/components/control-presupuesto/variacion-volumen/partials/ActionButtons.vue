<template>
    <div class="btn-group">
        <button @click="show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
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
