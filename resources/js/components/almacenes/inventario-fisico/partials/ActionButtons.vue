<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Ver Formato PDF"><i class="fa fa-file-pdf-o"></i> </button>
        <button @click="descargaLayout"  v-if="$root.can('descarga_layout_captura_conteos')" type="button" class="btn btn-sm btn-outline-success" title="Descargar Layout"><i class="fa fa-file-excel-o"></i> </button>
        <CreateMarbete v-if="value.show" @click="value.id" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import CreateMarbete from "../CreateMarbete";
    export default {
        name: "action-buttons",
        components: {CreateMarbete},
        props: ['value'],
        methods:{
            descargaLayout() {
                return this.$store.dispatch('almacenes/inventario-fisico/descargaLayout', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            show() {
                this.$router.push({name: 'create-marbete', params: {id: this.value.id}});
            },

        }
    }

</script>
