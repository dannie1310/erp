<template>
    <treeselect
            :class="{error: error}"
            :async="true"
            :load-options="loadOptions"
            v-model="val"
            loadingText="Cargando"
            searchPromptText="Escriba para buscar..."
            noResultsText="Sin Resultados"
            :placeholder="placeholder ? placeholder : '-- Buscar en Familia Materiales -- '"
    >
    </treeselect>
</template>

<script>
    export default {
        name: "familia-select",
        props: ['value', 'error', 'placeholder'],
        data() {
            return { val: null

            }
        },
        methods: {
            loadOptions({ action, searchQuery, callback }) {
                return this.$store.dispatch('cadeco/familia/index', {
                    params: {
                        search: searchQuery,
                        scope: 'tipo:1',
                        limit: 15,
                        sort: 'id_material',
                        order: 'ASC'
                    }
                })
                    .then(data => {

                        const options = data.data.map(i => ({
                            id: i.id,
                            label: i.descripcion
                        }))
                        callback(null, options)
                    })
            }
            // loadOptions({ action, searchQuery, callback }) {
            //     return this.$store.dispatch('almacenes/marbete/index', {
            //         params: {
            //             search: searchQuery,
            //             scope: 'inventarioAbierto',
            //             limit: 15,
            //             sort: 'folio',
            //             order: 'ASC'
            //         }
            //     })
            //         .then(data => {
            //             const options = data.map(i => ({
            //                 id: i.id,
            //                 label: i.folio_marbete
            //             }))
            //             callback(null, options)
            //         })
            // }
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
