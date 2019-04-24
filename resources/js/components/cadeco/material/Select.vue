<template>
    <span>
        <div v-if="disabled" class="form-control loading">
            <i class="fa fa-spin fa-spinner"></i>
        </div>

        <treeselect v-else
                :class="{error: error}"
                :instanceId="id"
                :multiple="multiple"
                loadingText="Cargando..."
                noOptionsText="No hay opciones disponibles"
                :options="rootNodes"
                :load-options="loadOptions"
                placeholder="-- Material --"
                :disableBranchNodes="this.disableBranchNodes"
                v-model="val"
        />
    </span>
</template>

<script>
    export default {
        props: ['value','id', 'multiple', 'error', 'scope', 'disableBranchNodes'],
        name: "material-select",
        data() {
            return {
                val: null,
                rootNodes: [],
                disabled: true
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
                    params: { scope: this.scp }
                })
                    .then(data => {
                        self.rootNodes = data.data.map(material => ({
                            id: material.id,
                            children: material.tiene_hijos != 0 ? null : undefined,
                            label: material.descripcion,
                        }));
                        self.disabled = false;
                    })
            },

            loadOptions({ action, parentNode, callback }) {
                return this.$store.dispatch('cadeco/material/find',{
                    id: parentNode.id,
                    params: { include: 'hijos', scope: this.scope }
                })
                    .then(data => {
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
            }
        },

        computed: {
            scp() {
                if (this.scope) {
                    return Array.isArray(this.scope) ? [...this.scope, 'roots'] : [this.scope, 'roots']
                } else {
                    return 'roots'
                }
            }
        }
    }
</script>
<style>
    .error > .vue-treeselect__control{
        border-color: #dc3545
    }

    .loading {
        text-align: center
    }
</style>