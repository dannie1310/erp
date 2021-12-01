<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <form role="form" @submit.prevent="validate">
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_partida">Buscar Subcontrato:</label>
                                            <model-list-select
                                                :disabled="cargando"
                                                name="id_partida"
                                                v-model="id_partida"
                                                option-value="num_partida"
                                                :custom-text="idFolioObservaciones"
                                                :list="subcontratos"
                                                :placeholder="!cargando?'Seleccionar o buscar por número de folio o referencia de subcontrato':'Cargando...'"
                                                :isError="errors.has(`id_partida`)">
                                            </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_partida')">{{ errors.first('id_partida') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="id_subcontrato > 0 && !subcontrato">
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                           <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                                <datos-subcontrato v-else v-bind:subcontrato="subcontrato" />
                            </div>
                             <div class="card-footer">
                                 <div class="row">
                                     <div class="col-md-12">
                                         <div class="pull-right">
                                             <button type="button" class="btn btn-secondary" v-on:click="salir">
                                                <i class="fa fa-angle-left"></i>
                                                Regresar</button>
                                            <button type="submit" :disabled="subcontrato === null" class="btn btn-primary">
                                                Continuar
                                                <i class="fa fa-angle-right"></i>
                                            </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosSubcontrato from "../estimacion/partials/DatosSubcontrato";
    export default {
        name: "estimacion-proveedor-seleccionar-subcontrato",
        components: {
            DatosSubcontrato,
            ModelListSelect},
        data() {
            return {
                cargando: false,
                id_partida: '',
                id_subcontrato: '',
                subcontratos : [],
                subcontrato : null,
            }
        },
        mounted() {
            this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
            this.$validator.reset();
            this.getSubcontratos();
        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[Subcontrato ${item.numero_folio_format} - [${item.proyecto}] ${item.referencia}]`;
            },
            salir()
            {
                this.$router.push({name: 'estimacion-proveedor'});
            },
            getSubcontratos(){
                this.subcontratos = [];
                this.cargando = true;

                return this.$store.dispatch('contratos/subcontrato/indexSinContexto', {
                    params:{ }
                }).then(data => {
                    this.subcontratos = data;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            find(base) {
                this.cargando = true;
                this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
                return this.$store.dispatch('contratos/subcontrato/findSinContexto', {
                    id: this.id_subcontrato,
                    base: base
                }).then(data => {
                    this.subcontrato = data;
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.$router.push({name: 'estimacion-proveedor-create', params: {id: this.id_subcontrato, base: this.subcontrato.base}});
                    }
                });
            },
        },
        watch: {
            id_partida(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.id_subcontrato = this.subcontratos[value]['id'];
                    this.find(this.subcontratos[value]['base']);
                }
            },
        }
    }
</script>

<style scoped>

</style>
