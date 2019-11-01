<template>
    <span>
        <div v-if="disabled" class="form-control text-center">
             <i class="fa fa-spin fa-spinner"></i>
         </div>

        <select class="form-control" v-if="!disabled" v-model="val" :class="{error: error}">
            <option disabled value>--Modelo--</option>
            <option v-for="modelo in modelos" :value="modelo.id">{{modelo.modelo}}</option>
        </select>
    </span>
</template>

<script>
    export default {
        name: "ModeloSelect",
        props:['value', 'error', 'scope'],
        data(){
            return {
                val: null,
                disabled: true
            }
        },
        mounted() {
            this.getModelos();
        },
        methods:{
            getModelos(){
                return this.$store.dispatch('sci/modelo/index',{
                    params: { sort: 'modelo', order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('sci/modelo/SET_MODELOS', data.data);
                        this.$store.commit('sci/modelo/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.disabled = false;
                    })
            }
        },
        computed: {
            modelos() {
                return this.$store.getters['sci/modelo/modelos']
            },
            meta(){
                return this.$store.getters['sci/modelo/meta']
            }
        },
        watch: {
            val() {
                this.$emit('input', this.val)
            },
            value(value) {
                if(!value) {
                    this.val = null;
                }
            }
        }
    }
</script>

<style scoped>

</style>
