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
                placeholder="-- Costo --"
                :disableBranchNodes="true"
                v-model="val"
        />
    </span>
</template>

<script>
    export default {
        props: ['value','id', 'multiple', 'error', 'scope'],
        name: "costo-select",
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
                return self.$store.dispatch('cadeco/costo/index', {
                    params: { scope: this.scp, sort: 'descripcion', order: 'ASC'}
                })
                    .then(data => {
                        self.rootNodes = data.data.map(costo => ({
                            id: costo.id,
                            children: costo.tiene_hijos != 0 ? null : undefined,
                            label: `${costo.descripcion} ${costo.observaciones ? '(' + costo.observaciones + ')' : ''}`,
                        }));
                        self.disabled = false;
                    })
            },

            loadOptions({ action, parentNode, callback }) {
                return this.$store.dispatch('cadeco/costo/find',{
                    id: parentNode.id,
                    params: { include: 'hijos', scope: this.scope, sort: 'descripcion', order: 'ASC' }
                })
                    .then(data => {
                        parentNode.children = data.hijos.data.map(costo => ({
                            id: costo.id,
                            children: costo.tiene_hijos != 0 ? null : undefined,
                            label: `${costo.descripcion} ${costo.observaciones ? '(' + costo.observaciones + ')' : ''}`,
                        }))
                        parentNode.children = _.sortBy(parentNode.children, 'label');
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