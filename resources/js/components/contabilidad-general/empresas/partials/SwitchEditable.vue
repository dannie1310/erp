<template>
    <div class="custom-control custom-switch" >
        <input type="checkbox" class="custom-control-input button"  :id="`switch_e${value.id}`" v-model="value.editable" @change="update" >
        <label class="custom-control-label" :for="`switch_e${value.id}`"></label>
    </div>
</template>

<script>
export default {
    name: "SwitchEditable",
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
                    Editable: this.value.editable ? 1 :0
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
