<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch${value.id}`" v-model="value.desarrollo" @change="update" >
        <label class="custom-control-label" :for="`switch${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchDesarrollo",
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
                    Desarrollo: this.value.desarrollo ? 1 :0
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
