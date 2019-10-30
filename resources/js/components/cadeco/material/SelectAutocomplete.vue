<template>
    <span>
          <treeselect
              :class="{error: error}"
              :async="true"
              :load-options="loadOptions"
              v-model="val"
              loadingText="Cargando"
              searchPromptText="Escriba para buscar..."
              noResultsText="Sin Resultados"
              :placeholder="placeholder ? placeholder : '--Material--'" :unidad="unidad" />

    </span>
<!--    <p>{{unidad}}</p>-->



</template>

<script>
    export default {
        props: ['scope', 'value', 'error', 'placeholder'],
        name: "SelectAutocomplete",
        data(){
            return {
                val: null,
                unidad:'',
                numero_parte:'',
                options:{}
            }
        },
        methods: {
            loadOptions({actions, searchQuery, callback}) {
                return this.$store.dispatch('cadeco/material/index',{
                    params: {
                        search: searchQuery,
                        scope: this.scope,
                        limit: 15
                    }
                })
                    .then(data => {
                        this.options = data.data.map(i => ({
                            id: i.id,
                            label: i.descripcion,
                            numero_parte: i.numero_parte,
                            unidad: i.unidad
                        }))
                        callback(null, this.options)
                    })
            },
            getMaterialInfo(){
                return this.$store.getters['cadeco/material/currentMaterial']
            }
        },
        watch: {
            val() {
                console.log("changing values");
                this.options.filter(x=> x.id === this.val).map(x => {
                    this.$store.commit('cadeco/material/SET_MATERIAL', x);
                    this.unidad = x.unidad,
                    this.numero_parte = x.numero_parte

                });
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
    .error > .vue-treeselect__control{
        border-color: #dc3545
    }
</style>
