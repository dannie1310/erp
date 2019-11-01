<template>
    <span>
        <div v-if="disabled" class="form-control text-center">
                  <i class="fa fa-spin fa-spinner"></i>
        </div>


    <select class="form-control" id="id_tipo" v-if="!disabled"  v-model="val" >
        <option selected>-- Almacen--</option>
       <option v-for="(almacen, index) in almacenes" :value="{value:almacen.id, text:almacen.descripcion }" >{{ almacen.descripcion}}</option>
    </select>


    </span>
</template>

<script>
    export default {
        name: "SelectAlmacenes",
        components: {},
        data(){
            return{
                val: null,
                disabled: true,
                text: null,

            }

        },
        mounted() {
            this.getTipos();

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
        },
        methods : {

            getTipos() {
                return this.$store.dispatch('cadeco/almacen/index', {
                    params: {sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('cadeco/almacen/SET_ALMACENES', data.data);
                        this.$store.commit('cadeco/almacen/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.disabled = false;
                    })
            },


        },
        computed: {
            almacenes(){
                 return this.$store.getters['cadeco/almacen/almacenes']
          },
            meta(){
                return this.$store.getters['cadeco/almacen/meta']
            }

        },

    }
</script>

<style scoped>

</style>
