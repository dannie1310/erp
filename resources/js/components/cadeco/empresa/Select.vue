<template>
    <treeselect
            :class="{error: error}"
            :async="true"
            :load-options="loadOptions"
            v-model="val"
            loadingText="Cargando"
            searchPromptText="Escriba para buscar..."
            noResultsText="Sin Resultados"
            :placeholder="placeholder ? placeholder :'-- Empresa --'"
    >
    </treeselect>
</template>

<script>
    export default {
        props: ['scope', 'value', 'error', 'placeholder'],
        name: "empresa-select",
        data() {
            return {
                val: null
            }
        },

        methods: {
            loadOptions({ action, searchQuery, callback }) {
                return this.$store.dispatch('cadeco/empresa/index', {
                    params: {
                        search: searchQuery,
                        scope: this.scope,
                        limit: 15
                    }
                })
                    .then(data => {
                        const options = data.data.map(i => ({
                            id: i.id,
                            label: i.razon_social
                        }))
                        callback(null, options)
                    })
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
    .error > .vue-treeselect__control{
        border-color: #dc3545
    }
</style>
