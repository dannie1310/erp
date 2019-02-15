<template>
    <treeselect
            :instanceId="id"
            :disabled="disabled"
            :multiple="multiple"
            loadingText="Cargando..."
            noOptionsText="No hay opciones disponibles"
            :options="rootNodes"
            :load-options="loadOptions"
            placeholder="[--SELECCIONE--]"
            :disableBranchNodes="true"
            v-model="val"
    />

</template>

<script>
    export default {
        props: ['value','id', 'multiple', 'disabled'],
        name: "material-select",
        data() {
            return {
                val: null,
                rootNodes: []
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
        },

        mounted() {
            this.getRootNodes();
        },

        methods: {
            getRootNodes() {
                let self = this
                return self.$store.dispatch('cadeco/material/index', {
                    params: { scope: 'roots' }
                })
                    .then(data => {
                        self.rootNodes = data.map(material => ({
                            id: material.id,
                            children: material.tiene_hijos != 0 ? null : undefined,
                            label: material.descripcion,
                        }));
                    })
            },

            loadOptions({ action, parentNode, callback }) {
                return this.$store.dispatch('cadeco/material/find',{
                    id: parentNode.id,
                    params: { include: 'hijos' }
                })
                    .then(data => {
                        console.log(data)
                        parentNode.children = data.hijos.data.map(material => ({
                            id: material.id,
                            children: material.tiene_hijos != 0 ? null : undefined,
                            label: `[${material.numero_parte ? material.numero_parte : 'N/A'}] ${material.descripcion}`
                        }))
                    })
                    .then(() => {
                        callback();
                    })
                    .catch(error => {
                        callback(new Error('Failed to load options: network error.'))
                    });
            },
        }
    }
</script>
