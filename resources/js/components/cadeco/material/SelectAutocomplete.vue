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
              :placeholder="placeholder ? placeholder : '--Material--'" />

    </span>
</template>

<script>
    export default {
        props: ['scope', 'value', 'error', 'placeholder'],
        name: "SelectAutocomplete",
        data(){
            return {
                val: null,
                material: [],
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
                            unidad: i.unidad,
                        }))
                        callback(null, this.options)
                    })
            }
        },
        watch: {
            val() {
                this.options.filter(x=> x.id === this.val).map(x => {
                    this.material = x;
                });
                this.$emit('input', this.material)
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
