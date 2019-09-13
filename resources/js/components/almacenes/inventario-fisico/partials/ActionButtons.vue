<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Descargar Marbetes" v-if="value.marbete" @click="pdf_marbetes(value.id)">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-file-pdf-o" v-else></i>
        </button>
        <button @click="descargaLayout"  v-if="$root.can('descarga_layout_captura_conteos')" type="button" class="btn btn-sm btn-outline-success" title="Descargar Layout">
            <i class="fa fa-file-excel-o"></i>
        </button>
        <button @click="descarga_resumen"  v-if="value.resumen" type="button" class="btn btn-sm btn-outline-success" title="Descargar Resumen Conteos">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-download" v-else></i>
        </button>

    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
        props: ['value'],
        data() {
            return {
                cargando: false
            }
        },
        methods:{
            pdf_marbetes(id) {
                this.cargando = true;
                return this.$store.dispatch('almacenes/inventario-fisico/pdf_marbetes', {id:id})
                    .then(data => {
                        this.$emit('success');
                    }).finally(() => {
                        this.cargando = false;
                    })
            },
            descargaLayout() {
                return this.$store.dispatch('almacenes/inventario-fisico/descargaLayout', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            descarga_resumen(){
                this.cargando = true;
                return this.$store.dispatch('almacenes/inventario-fisico/descargar_resumen_conteos', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    }).finally(() => {
                        this.cargando = false;
                    })
            }
        }
    }

</script>
