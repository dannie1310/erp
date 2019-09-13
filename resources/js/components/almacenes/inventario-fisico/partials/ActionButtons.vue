<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Descargar Marbetes" v-if="value.marbete" @click="pdf_marbetes(value.id)"><i class="fa fa-file-pdf-o"></i> </button>
        <button @click="descargaLayout"  v-if="$root.can('descarga_layout_captura_conteos')" type="button" class="btn btn-sm btn-outline-success" title="Descargar Layout"><i class="fa fa-file-excel-o"></i> </button>
        <Layout v-if="value.estado == 0 && $root.can('cargar_layout_captura_conteos')" v-bind:id="value.id">{{value.estado}}</Layout>
    </div>
</template>

<script>
    import Layout from "../../conteo/cargar-layout";
    export default {
        name: "action-buttons",
        components: {Layout},
        props: ['value'],
        methods:{
            pdf_marbetes(id) {
                return this.$store.dispatch('almacenes/inventario-fisico/pdf_marbetes', {id:id})
                    .then(data => {
                    }).finally( ()=>{
                    });
            },
            descargaLayout() {
                return this.$store.dispatch('almacenes/inventario-fisico/descargaLayout', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            }
        }
    }

</script>
