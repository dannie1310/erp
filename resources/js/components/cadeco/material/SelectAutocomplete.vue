<template>
    <treeselect
        :class="{error: error}"
        :async="true"
        :load-options="loadOptions"
        v-model="val"
        :loadingText="loadingText"
        searchPromptText="Escriba al menos 4 carácteres para buscar"
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
            disabled: true,
            loadingText: "Cargando"
        }
    },
    methods: {
        loadOptions({actions, searchQuery, callback}) {
            if(searchQuery.length>4){
                return this.$store.dispatch('cadeco/material/index',{

                    params: {
                        search: searchQuery,
                        sort: this.sort,
                        scope: this.scope,
                        limit: 500
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
            }else {
                this.loadingText = "Escriba al menos 4 carácteres para buscar"
            }
        }

    },
    watch: {
        val() {
            if(this.val !== null && this.val !== undefined){

                var material = this.options.find(x=>x.id === this.val);
                if(material != undefined)
                {
                    setTimeout(() => {
                        this.showSelect = false;
                        this.$emit('input', material)
                    }, 0);
                }
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