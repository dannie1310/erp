<template>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" title="Descargar Marbetes" v-if="value.estado == 0 && value.marbete" @click="pdf_marbetes(value.id)">
            <i class="fa fa-file-pdf-o"></i>
        </button>
        <button @click="descargaLayout"  v-if="value.estado == 0 && $root.can('descarga_layout_captura_conteos')" type="button" class="btn btn-sm btn-outline-success" title="Descargar Layout">
            <i class="fa fa-download"></i>
        </button>
        <Layout v-if="value.estado == 0 && $root.can('cargar_layout_captura_conteos')" v-bind:id="value.id">{{value.estado}}</Layout>
        <button @click="descarga_resumen"  v-if="value.resumen" type="button" class="btn btn-sm btn-outline-primary" title="Descargar Resumen Conteos">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-file-excel-o" v-else></i>
        </button>
        <button @click="update" v-if="$root.can('cerrar_inventario_fisico') && value.estado == 0" type="button" class="btn btn-sm btn-outline-danger" title="Cerrar Inventario Físico"><i class="fa fa-lock"></i> </button>
        <CreateMarbete v-if="value.show" @click="value.id" v-bind:id="value.id"/>
    </div>
</template>

<script>
    import CreateMarbete from "../CreateMarbete";
    import Layout from "../../conteo/cargar-layout";
    export default {
        name: "action-buttons",
        components: {CreateMarbete, Layout},
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
            },
            update() {
                return this.$store.dispatch('almacenes/inventario-fisico/update', {id: this.value.id})
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
