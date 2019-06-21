<template>
    <treeselect
            :class="{error: error}"
            :async="true"
            :load-options="loadOptions"
            v-model="val"
            loadingText="Cargando"
            searchPromptText="Escriba para buscar..."
            noResultsText="Sin Resultados"
            placeholder="-- Subcontrato --"
    >
    </treeselect>
</template>

<script>
    export default {
        props: ['scope', 'value', 'error'],
        name: "subcontrato-select",
        data() {
            return {
                val: null
            }
        },
        methods: {
            loadOptions({ action, searchQuery, callback }) {
                return this.$store.dispatch('contratos/subcontrato/index', {
                    config: {
                        params: {
                            search: searchQuery,
                            scope: this.scope,
                            limit: 15
                        }
                    }
                })
                    .then(data => {
                        const options = data.map(i => ({
                            id: i.id,
                            label: i.numero_folio_format + ' - Referencia: [' +i.referencia + ']'
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
