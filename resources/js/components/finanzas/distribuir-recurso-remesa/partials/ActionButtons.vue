<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
        <button @click="autorizar" v-if="value.autorizar" type="button" class="btn btn-sm btn-outline-primary" title="Autorizar"><i class="fa fa-check-square"></i></button>
        <a :href="url" target="_blank" v-if="value.pagar && (value.estado === 0)" type="button" class="btn btn-sm btn-outline-info" title="Pagar"><i class="fa fa-money"></i></a>
        <a :href="urlmanual" target="_blank" v-if="value.pagar && (value.estado === 0)" type="button" class="btn btn-sm btn-outline-info" title="Layout"><i class="fa fa-file-excel-o"></i></a>
        <!--<DistribuirRecursoRemesaEdit v-if="value.edit" v-bind:id="value.id" />-->
        <button @click="cancelar" v-if="value.cancelar && (value.estado === 0)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar"><i class="fa fa-ban"></i></button>
    </div>
</template>

<script>
    export default {
        name: "action-buttons",
        components: {},
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
            autorizar() {
                this.$router.push({name: 'distribuir-recurso-remesa-autorizar', params: {id: this.value.id}});
            }
        },
        computed: {
            url(){
                return '/api/finanzas/distribuir-recurso-remesa/' + this.value.id +'/layout?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra') + '&access_token=' + this.$session.get('jwt'); //access_token=' + this.$session.get('jwt')

            },
            urlmanual(){
                return '/finanzas/distribuir-recurso-remesa/' + this.value.id +'/layoutManual?db=' + this.$session.get('db') + '&idobra=' + this.$session.get('id_obra');
            }
        },
        mounted() {
        }
    }
</script>
