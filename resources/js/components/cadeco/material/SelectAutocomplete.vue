<template>
    <treeselect
              :class="{error: error}"
              :async="true"
              :load-options="loadOptions"
              v-model="val"
              loadingText="Cargando"
              searchPromptText="Escriba para buscar..."
              noResultsText="Sin Resultados"
              :placeholder="placeholder ? placeholder : '--Material--'">
                <label slot="option-label" slot-scope="{ node, shouldShowCount, count, labelClassName, countClassName }" :class="labelClassName" :title="node.label">
                    {{ node.label }}
                </label>
          </treeselect>
</template>

<script>
    export default {
        name: "SelectAutocomplete",
        props: ['scope', 'sort', 'value', 'error', 'placeholder'],
        data(){
            return {
                val: null,
                material: [],
                options:{},
                disabled: true
            }
        },
        methods: {
            loadOptions({actions, searchQuery, callback}) {
                return this.$store.dispatch('cadeco/material/index',{
                    params: {
                        search: searchQuery,
                        sort: this.sort,
                        scope: this.scope,
                        limit: 100
                    }
                })
                    .then(data => {
                        this.disabled = false;
                        this.options = data.data.map(i => ({
                            id: i.id,
                            label: i.numero_parte_descripcion,
                            descripcion: i.descripcion,
                            numero_parte: i.numero_parte,
                            unidad: i.unidad
                        }))

                        callback(null, this.options)
                    })
            }
        },
        watch: {
            val() {
                if(this.val !== null && this.val !== undefined){
                this.options.filter(x=> x.id === this.val).map(x => {
                    this.material = x;
                });
                setTimeout(() => {
                        this.$emit('input', this.material)
                    }, 0);
                }
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
