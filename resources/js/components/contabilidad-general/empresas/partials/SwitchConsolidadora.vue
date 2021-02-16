<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch_c${value.id}`" v-model="value.consolidadora" @change="update" >
        <label class="custom-control-label" :for="`switch_c${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchConsolidadora",
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
                    Consolidadora: this.value.consolidadora ? 1 :0
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
