<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Descargar Marbetes" v-if="value.marbete" @click="pdf_marbetes(value.id)"><i class="fa fa-file-pdf-o"></i> </button>
        <button @click="descargaLayout"  v-if="$root.can('descarga_layout_captura_conteos')" type="button" class="btn btn-sm btn-outline-success" title="Descargar Layout"><i class="fa fa-file-excel-o"></i> </button>
        <button @click="update" v-if="$root.can('cerrar_inventario_fisico') && value.estado == 0" type="button" class="btn btn-sm btn-outline-success" title="Cerrar Inventario Físico"><i class="fa fa-lock"></i> </button>

    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
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
            },
            update() {
                return this.$store.dispatch('almacenes/inventario-fisico/update', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            }
        }
    }

</script>
