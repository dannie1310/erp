<template>
    <div class="btn-group">
        <router-link  :to="{ name: 'poliza-contpaq-show', params: {id: value.id}}" v-if="$root.can('consultar_poliza_ctpq',1)" type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link  :to="{ name: 'poliza-contpaq-edit', params: {id: this.value.id, id_empresa: this.value.id_empresa}}" v-if="$root.can('editar_poliza_ctpq',1)" type="button" class="btn btn-sm btn-outline-warning" title="Ver">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link  :to="{ name: 'poliza-contpaq-asociar-cfdi', params: {id: this.value.id, id_empresa: this.value.id_empresa}}" v-if="$root.can('editar_poliza_ctpq',1)" type="button" class="btn btn-sm btn-outline-warning" title="Asociar CFDI">
            <i class="fa fa-share-alt"></i>
        </router-link>
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
        },
    }
</script>

