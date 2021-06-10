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
                placeholder="Nodo de Contrato"
                :disableBranchNodes="disableBranchNodes"
                v-model="val"
        />
    </span>
</template>

<script>
    export default {
        props: ['value','id', 'multiple', 'error', 'scope', 'disableBranchNodes', 'idContratoProyectado'],
        name: "SelectContrato",
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
                return self.$store.dispatch('contratos/contrato-concepto/index', {
                    params: { scope: ["agrupadorExtraordinario", "contrato:"+this.idContratoProyectado] }
                })
                    .then(data => {
                        self.rootNodes = data.map(contrato => ({
                            id: contrato.id,
                            children: contrato.tiene_hijos != 0 ? null : undefined,
                            label: contrato.clave_contrato_select + contrato.descripcion,
                            isDisabled: contrato.tiene_hijos ==0 ?true :false
                        }));
                        self.disabled = false;
                    })
            },

            loadOptions({ action, parentNode, callback }) {
                return this.$store.dispatch('contratos/contrato-concepto/find',{
                    id: parentNode.id,
                    params: { include: 'hijos', scope: this.scope }
                })
                    .then(data => {
                        parentNode.children = data.hijos.data.map(concepto => ({
                            id: concepto.id,
                            children: concepto.tiene_hijos != 0 ? null : undefined,
                            label: concepto.clave_contrato_select + concepto.descripcion,
                            isDisabled: concepto.tiene_hijos ==0 ?true :false
                        }))
                    })
                    .then(() => {
                        callback();
                    })
                    .catch(error => {
                        callback(new Error('Falla en carga de opciones'))
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
