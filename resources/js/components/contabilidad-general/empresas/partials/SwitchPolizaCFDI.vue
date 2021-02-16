<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch_p${value.id}`" v-model="value.poliza_cfdi" @change="update" >
        <label class="custom-control-label" :for="`switch_p${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchPolizaCFDI",
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
                    SincronizacionPolizasCFDI: this.value.poliza_cfdi ? 1 :0
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
