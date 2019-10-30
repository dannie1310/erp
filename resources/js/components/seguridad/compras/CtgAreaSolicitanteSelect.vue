<template>
    <span>
        <div v-if="disabled" class="form-control">
            <i class="fa fa-spin fa-spinner"></i>
        </div>

        <select class="form-control" v-if="!disabled" v-model="val">
       <option disabled value>-- Área Solicitante--</option>
       <option v-for="area in areas" :value="area.id" >{{ area.descripcion}}</option>
        </select>

    </span>
</template>

<script>
    export default {
        name: "CtgAreaSolicitanteSelect",
        components: {},
        data(){
            return{
                val:null,
                disabled:true,
            }
        },
        mounted() {
            this.getAreasSolicitantes();
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
        methods: {
            getAreasSolicitantes(){
                return this.$store.dispatch('seguridad/compras/ctg-area-solicitante/index', {
                    params: { scope:'',sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('seguridad/compras/ctg-area-solicitante/SET_AREAS', data.data);
                        this.$store.commit('seguridad/compras/ctg-area-solicitante/SET_META', data.meta);
                        this.disabled = false;
                    })
            }

        },
        computed: {
            areas(){
                return this.$store.getters['seguridad/compras/ctg-area-solicitante/areas']
            },
            meta(){
                return this.$store.getters['seguridad/compras/ctg-area-solicitante/meta']
            }
        }
    }
</script>

<style scoped>

</style>
