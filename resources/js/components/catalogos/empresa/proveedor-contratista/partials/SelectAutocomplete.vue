<template>
    <treeselect
        :class="{error: error}"
        :async="true"
        :load-options="loadOptions"
        v-model="val"
        :loadingText="loadingText"
        loadingText="Cargando"
        searchPromptText="Escriba al menos 3 carácteres para buscar"
        noResultsText="Sin Resultados"
        :placeholder="placeholder ? placeholder : '--Proveedor--'">
        <label slot="option-label" slot-scope="{ node, shouldShowCount, count, labelClassName, countClassName }" :class="labelClassName" :title="node.label">
            {{ node.label }}
        </label>
    </treeselect>
</template>

<script>
export default {
    name: "ProveedorContratistaSelectAutocomplete",
    props: ['scope', 'sort', 'value', 'error', 'placeholder'],
    data(){
        return {
            val: null,
            proveedor: [],
            options:{},
            disabled: true,
            loadingText: "Cargando"
        }
    },
    methods: {
        loadOptions({actions, searchQuery, callback}) {
            if(searchQuery.length>2){
                return this.$store.dispatch('cadeco/empresa/index',{
                    params: {
                        search: searchQuery,
                        limit: 15,
                        sort: 'razon_social', order: 'asc',
                        scope:'tipoEmpresa:1,3', include: 'sucursales'
                    }
                })
                    .then(data => {
                        this.disabled = false;
                        this.options = data.data.map(i => ({
                            id: i.id,
                            label: i.rfc + "-" +i.razon_social,
                            customLabel: i.rfc + "-" + i.razon_social,
                            sucursales : i.sucursales
                        }))
                        callback(null, this.options)
                    })
            }else {
                this.loadingText = "Escriba al menos 3 carácteres para buscar"
            }
        },

    },
    watch: {
        val() {
            if(this.val !== null && this.val !== undefined){

                var proveedor = this.options.find(x=>x.id === this.val);
                if(proveedor != undefined)
                {
                    setTimeout(() => {
                        this.showSelect = false;
                        this.$emit('input', proveedor)
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
