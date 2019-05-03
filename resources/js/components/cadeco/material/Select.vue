<template>
    <span>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control" v-model="tipo_material">
                    <option disabled value>-- Tipo de Material --</option>
                    <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                </select>
            </div>

            <div class="col-md-6">
                <div v-if="cargando" class="form-control loading">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
                <treeselect v-else
                        :disabled="!tipo_material"
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

            </div>
        </div>
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
                disabled: true,
                tipo_material: '',
                tipos: [
                    {id: 1, descripcion: 'Materiales'},
                    {id: 2, descripcion: 'Mano de Obra y Servicios'},
                    {id: 4, descripcion: 'Herramienta y Equipo'},
                    {id: 8, descripcion: 'Maquinaria'}
                ],
                cargando: false
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
            },
            tipo_material(tipo) {
                if(tipo) {
                    this.val = null
                    this.getRootNodes()
                        .finally(() => {
                            this.$validator.reset()
                        });
                }
            }
        },

        methods: {
            getRootNodes() {
                let self = this
                self.rootNodes = [];
                this.cargando = true;
                return self.$store.dispatch('cadeco/material/index', {
                    params: {
                        scope: this.scp,
                        sort: 'descripcion',
                        order: 'ASC'
                    }
                })
                    .then(data => {
                        self.rootNodes = data.data.map(material => ({
                            id: material.id,
                            children: material.tiene_hijos != 0 ? null : undefined,
                            label: material.descripcion,
                        }));
                        self.disabled = false;
                    })
                    .finally(() => {
                        this.cargando = false;
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
                    return Array.isArray(this.scope) ? [...this.scope, 'roots', `tipo:${this.tipo_material}`] : [this.scope, 'roots', `tipo:${this.tipo_material}`]
                } else {
                    return ['roots', `tipo:${this.tipo_material}`]
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