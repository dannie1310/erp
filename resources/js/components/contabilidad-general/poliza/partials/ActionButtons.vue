<template>
    <div class="btn-group">
        <Show  v-bind:id="value.id" v-bind:id_empresa="value.id_empresa" />
        <button @click="edit" type="button" class="btn btn-sm btn-outline-primary  " title="Editar" v-if="value.editar">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
        <PDFPoliza v-bind:id="value.id" v-bind:id_empresa="value.id_empresa"></PDFPoliza>
    </div>
</template>

<script>
    import PDFPoliza from './PDFPoliza';
    import Show from '../Show'
    export default {
        name: "action-buttons",
        components:{Show, PDFPoliza},
        props: ['value'],
        methods: {
            edit() {
                this.$router.push({ name:'poliza-contpaq-edit', params: {id: this.value.id, id_empresa: this.value.id_empresa}});
            },
        },
    }
</script>

