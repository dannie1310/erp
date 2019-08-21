<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button @click="autorizar" v-if="value.autorizar && value.estado === 0" type="button" class="btn btn-sm btn-outline-primary" title="Autorizar"><i class="fa fa-check-square"></i></button>
        <button @click="url" v-if="value.pagar && (value.estado === 1)" type="button" class="btn btn-sm btn-outline-info" title="Pagar"><i class="fa fa-money"></i></button>
        <button @click="urlmanual" v-if="value.descargar && (value.estado === 1)" type="button" class="btn btn-sm btn-outline-info" title="Descargar"><i class="fa fa-download"></i></button>
<!--        <a :href="url" target="_blank" v-if="value.pagar && (value.estado === 1)" type="button" class="btn btn-sm btn-outline-info" title="Pagar"><i class="fa fa-money"></i></a>-->
<!--        <a :href="urlmanual" target="_blank" v-if="value.pagar && (value.estado === 1)" type="button" class="btn btn-sm btn-outline-info" title="Layout"><i class="fa fa-file-excel-o"></i></a>-->
        <button @click="cancelar" v-if="value.cancelar && (value.estado === 0)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
<!--        <DistibucionCargaLayout v-if="value.cargar && (value.estado === 2)" v-bind:id="value.id" />-->
    </div>
</template>

<script>
    import DistibucionCargaLayout from "../CargaLayout";
    export default {
        name: "action-buttons",
        components: {DistibucionCargaLayout},
        props: ['value'],
        methods: {
            cancelar() {
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/cancel', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            destroy() {

            },
            show() {
                this.$router.push({name: 'distribuir-recurso-remesa-show', params: {id: this.value.id}});
            },
            url(){
                let self = this;
                return self.$store.dispatch('finanzas/distribuir-recurso-remesa/descarga', {id: this.value.id})
                    .then(data => {
                        this.$emit('success')
                    });
            },
            urlmanual(){
                return this.$store.dispatch('finanzas/distribuir-recurso-remesa/descargaManual', {id: this.value.id})
                    .then(() => {
                        this.$emit('success')
                    })
            },
            autorizar() {
                this.$router.push({name: 'distribuir-recurso-remesa-autorizar', params: {id: this.value.id}});
            }
        },
        computed: {

        },
        mounted() {
        }
    }
</script>
