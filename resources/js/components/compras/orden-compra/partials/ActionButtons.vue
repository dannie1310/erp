<template>
    <div class="btn-group">

        <div class="modal-footer btn-group" v-if="value.id">
            <PDF v-bind:id="value.id" @click="value.id"></PDF>
        </div>

        <div class="modal-footer btn-group" v-else>
            <button @click="validate" v-if="value.pdf" type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF"><i class="fa fa-file-pdf-o"></i> </button>
        </div>

    </div>
</template>

<script>

    import PDF from './FormatoOrdenCompra';
    export default {
        name: "action-buttons",
        components: {PDF},
        props: ['value'],
        methods: {
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {

                    }
                });
            }
        }
    }
</script>