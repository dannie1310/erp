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
                :placeholder="placeholder"
                :disableBranchNodes="disableBranchNodes"
                v-model="val"

        >
        <label slot="option-label" slot-scope="{ node, shouldShowCount, count, labelClassName, countClassName }" :class="labelClassName" :title="node.label">
            {{ node.label }}
        </label>
        </treeselect>
    </span>
</template>

<script>
    export default {
        props: ['value','id', 'multiple', 'error', 'scope', 'disableBranchNodes','placeholder'],
        name: "concepto-select",
        data() {
            return {
                val: null,
                text:null,
                rootNodes: [],
                disabled: true,
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
                return self.$store.dispatch('cadeco/concepto/index', {
                    params: { scope: this.scp }
                })
                    .then(data => {
                        self.rootNodes = data.data.map(concepto => ({
                            id: concepto.id,
                            children: concepto.tiene_hijos != 0 ? null : undefined,
                            label: concepto.clave_concepto_select + concepto.descripcion,
                            isDisabled : concepto.deshabilitadoPadreMedibles == 1 ? true : false
                        }));
                        self.disabled = false;
                    })
            },

            loadOptions({ action, parentNode, callback }) {
                return this.$store.dispatch('cadeco/concepto/find',{
                    id: parentNode.id,
                    params: { include: 'hijos', scope: this.scope }
                })
                    .then(data => {
                        parentNode.children = data.hijos.data.map(concepto => ({
                            id: concepto.id,
                            children: concepto.tiene_hijos != 0 ? null : undefined,
                            label: concepto.clave_concepto_select + concepto.descripcion,
                            isDisabled : concepto.deshabilitadoPadreMedibles == 1 ? true : false
                        }))
                    })
                    .then(() => {
                        callback();
                    })
                    .catch(error => {
                        callback(new Error('Fallo al cargar opciones: error de red.'))
                    });
            },

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
