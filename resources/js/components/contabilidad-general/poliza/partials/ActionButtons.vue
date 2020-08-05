<template>
    <div class="btn-group">
        <button @click="show"  type="button" class="btn btn-sm btn-outline-secondary" title="Ver" v-bind:id="value.id" v-bind:id_empresa="value.id_empresa">
            <i class="fa fa-eye"></i>
        </button>
        <button @click="edit" type="button" class="btn btn-sm btn-outline-primary  " title="Editar" v-if="value.editar">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </button>
        <PDFPoliza v-bind:id="value.id" v-bind:id_empresa="value.id_empresa"></PDFPoliza>
    </div>
</template>

<script>
    import PDFPoliza from './PDFPoliza';
    export default {
        name: "action-buttons",
        components:{PDFPoliza},
        props: ['value'],
        methods: {
            show() {
                this.$router.push({ name:'poliza-contpaq-show', params: {id: this.value.id}});
               /* this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', null);
                return this.$store.dispatch('contabilidadGeneral/poliza/find', {
                    id: id,
                    params: {include: ['movimientos_poliza'], id_empresa : this.value.id_empresa}
                }).then(data => {
                    data.tipo_modal = 1;
                    this.$store.commit('contabilidadGeneral/poliza/SET_POLIZA', data);
                })*/
            },
            edit() {
                this.$router.push({ name:'poliza-contpaq-edit', params: {id: this.value.id, id_empresa: this.value.id_empresa}});
            },
        },
    }
</script>

