<template>
<!--    <treeselect-->
<!--        :class="{error: error}"-->
<!--        :async="true"-->
<!--        :load-options="loadOptions"-->
<!--        v-model="val"-->
<!--        loadingText="Cargando"-->
<!--        searchPromptText="Escriba para buscar..."-->
<!--        noResultsText="Sin Resultados"-->
<!--        :placeholder="placeholder ? placeholder : '&#45;&#45;Marcas&#45;&#45;'" />-->
    <span>
         <div v-if="disabled" class="form-control text-center">
             <i class="fa fa-spin fa-spinner"></i>
         </div>

        <select class="form-control" v-if="!disabled" v-model="val" :class="{error: error}">
            <option disabled value>--Marca--</option>
            <option v-for="marca in marcas" :value="marca.id">{{marca.marca}}</option>
        </select>
    </span>
</template>

<script>
    export default {
        name: "MarcaSelect",
        props: ['scope', 'value', 'error', 'placeholder'],
        data(){
            return {
                val:null,
                disabled: true,
            }
        },
        mounted() {
            this.getMarcas();
        },

        methods : {
            loadOptions({actions, searchQuery, callback}){
                this.$store.dispatch('sci/marca/index', {
                    params: {
                        search: searchQuery,
                        scope: this.scope,
                        limit: 15
                    }
                })
                    .then(data => {
                        const options = data.data.map(i => ({
                            id: i.id,
                            label: i.marca,
                        }))
                        callback(null, options)
                    })

            },
            getMarcas() {
                return this.$store.dispatch('sci/marca/index',{
                    params: { sort: 'marca', order: 'asc'}
                })
                    .then(data => {
                        this.$store.commit('sci/marca/SET_MARCAS', data.data);
                        this.$store.commit('sci/marca/SET_META', data.meta);
                    })
                    .finally(() => {
                        this.disabled = false;
                    })
            }
        },
        computed: {
            marcas() {
                return this.$store.getters['sci/marca/marcas']
            },
            meta() {
                return this.$store.getters['sci/marca/meta']
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

<style>
    .error {
        border-color: #dc3545
    }

</style>
