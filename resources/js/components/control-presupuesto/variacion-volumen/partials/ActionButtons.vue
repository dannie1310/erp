<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button @click="pdf"  v-if="value.pdf" type="button" class="btn btn-sm btn-outline-info" title="PDF"><i class="fa fa-file-pdf-o"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({name: 'variacion-volumen-show', params: {id: this.value.id}});
            },
            pdf() {
                // this.$router.push({name: 'poliza-edit', params: {id: this.value.id}});
            }
        },
        computed: {
        },
        mounted() {
        }
    }
</script>
