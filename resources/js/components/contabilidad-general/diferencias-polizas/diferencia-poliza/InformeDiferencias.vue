<template>
    <span>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="form-row">
                            <div class="col">
                                <select class="form-control" v-model="id_empresa">
                                    <option value>-- Empresa --</option>
                                    <option v-for="item in empresas" v-bind:value="item.id">{{ item.nombre }}</option>
                                </select>
                            </div>
                            <div class="col">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="solicitud_relacionada"  v-model="sin_solicitud_relacionada">
                                    <label for="solicitud_relacionada" class="custom-control-label" v-model="sin_solicitud_relacionada">Sin Solicitud Relacionada</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="diferencias_activas"  v-model="solo_diferencias_activas">
                                    <label for="diferencias_activas" class="custom-control-label" v-model="solo_diferencias_activas">Sólo Diferencias Activas</label>
                                </div>
                            </div>
                            <div class="col">
                                <button @click="consultar" class="btn btn-primary float-right" :disabled="cargando">
                                    <i class="fa fa-spinner" v-if="cargando"></i>
                                    <i class="fa fa-search" v-else></i>Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "InformeDiferencias",
        data() {
            return {
                id_empresa:'',
                empresas :[],
                informe : [],
                sin_solicitud_relacionada : 1,
                solo_diferencias_activas : 1,
                cargando : false
            }
        },
        mounted() {
            this.getEmpresas();
        },
        methods :{
            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                    params: {
                        sort: 'Nombre',
                        order: 'asc',
                        scope:'conDiferencias',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },
            consultar() {
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/incidente-poliza/obtenerInforme', {
                    id_empresa: this.id_empresa,
                    sin_solicitud_relacionada : this.sin_solicitud_relacionada,
                    solo_diferencias_activas : this.solo_diferencias_activas,
                })
                    .then(data => {
                        this.informe = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>