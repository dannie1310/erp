<template>
    <span>
        <div v-if="disabled" class="form-control text-center">
                  <i class="fa fa-spin fa-spinner"></i>
        </div>


    <select class="form-control" id="id_tipo" v-if="!disabled"  v-model="val" :class="{error: error}" >
        <option disabled value>-- Tipo --</option>
       <option v-for="(tipo, index) in tipos" :value="tipo.id" >{{ tipo.descripcion}}</option>
    </select>


    </span>
</template>

<script>
    export default {
        name: "CtgTipoSelect",
        props:['value', 'error', 'scope'],
        components: {},
        data(){
            return{
                val: null,
                disabled: true,

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
                return this.$store.dispatch('seguridad/compras/ctg-tipo/index', {
                    params: {sort: 'descripcion',  order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('seguridad/compras/ctg-tipo/SET_TIPOS', data.data);
                        this.$store.commit('seguridad/compras/ctg-tipo/SET_META', data.meta);
                        this.disabled = false;
                    })
            },


        },
        computed: {
            tipos(){
                return this.$store.getters['seguridad/compras/ctg-tipo/tipos']
            },
            meta(){
                return this.$store.getters['seguridad/compras/ctg-tipo/meta']
            }
        },

    }
</script>

<style>
    .error {
        border-color: #dc3545
    }
</style>
