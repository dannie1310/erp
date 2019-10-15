<template>
    <treeselect
        :class="{error: error}"
        :async="true"
        :load-options="loadOptions"
        v-model="val"
        loadingText="Cargando"
        searchPromptText="Escriba para buscar..."
        noResultsText="Sin Resultados"
        :placeholder="placeholder ? placeholder : '-- Buscar en Familia Servicio -- '"
    >
    </treeselect>
</template>

<script>
    export default {
        name: "familia-select",
        props: ['scope','value', 'error', 'placeholder'],
        data() {
            return { val: null

            }
        },
        methods: {
            loadOptions({ action, searchQuery, callback }) {
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {
                        search: searchQuery,
                        scope: this.scope,
                        limit: 15,
                        sort: 'nivel',
                        order: 'ASC'
                    }
                })
                    .then(data => {

                        const options = data.data.map(i => ({
                            id: i.nivel,
                            label: i.descripcion
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
