<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch_h${value.id}`" v-model="value.historica" @change="update" >
        <label class="custom-control-label" :for="`switch_h${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchHistorica",
    props: ['value'],
    data() {
        return {
        }
    },

    methods: {
        update(){
            return this.$store.dispatch('seguridad/lista-empresas/updateDirecto', {
                id: this.value.id,
                params:{
                    Historica: this.value.historica ? 1 :0
                }
            }).then(data => {
                this.$store.commit('contabilidadGeneral/empresa/UPDATE_EMPRESA', data);
            })
        },
    },
}
</script>

<style scoped>

</style>
