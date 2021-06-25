<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch_v${value.id}`" v-model="value.visible" @change="update" >
        <label class="custom-control-label" :for="`switch_v${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchVisible",
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
                    Visible: this.value.visible ? 1 :0
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
