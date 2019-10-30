<template>
    <span>
         <div v-if="disabled" class="form-control">
             <i class="fa fa-spin fa-spinner"></i>
         </div>

        <select class="form-control" v-if="!disabled" v-model="val">
           <option disabled value>-- Área Compradora--</option>
           <option v-for="area in areas" :value="area.id" >{{ area.descripcion}}</option>
        </select>

    </span>
</template>

<script>
    export default {
        name: "CtgAreaCompradoraSelect",
        components:{},
        data(){
            return{
                val:null,
                disabled:true,
            }
        },
        mounted(){
            this.getAreasCompradoras();
        },
        watch: {
            val() {
                this.$emit('input', this.val)
            },
            value(value){
                if(!value) {
                    this.val = null;
                }
            }
        },
        methods:{
            getAreasCompradoras(){
                return this.$store.dispatch('seguridad/compras/ctg-area-compradora/index', {
                    params: { sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('seguridad/compras/ctg-area-compradora/SET_AREAS', data.data);
                        this.$store.commit('seguridad/compras/ctg-area-compradora/SET_META', data.meta);
                        this.disabled = false;
                    })
            }
        },
        computed: {
            areas(){
                return this.$store.getters['seguridad/compras/ctg-area-compradora/areas']
            },
            meta(){
                return this.$store.getters['seguridad/compras/ctg-area-compradora/meta']
            }
        }
    }
</script>

<style scoped>

</style>
