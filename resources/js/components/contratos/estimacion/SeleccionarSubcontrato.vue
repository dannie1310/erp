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
                                            <label for="id_subcontrato">Buscar Subcontrato:</label>
                                            <model-list-select
                                                :disabled="cargando"
                                                name="id_subcontrato"
                                                option-value="id"
                                                v-model="id_subcontrato"
                                                :custom-text="numeroFolioAndRefernciaAndEmpresa"
                                                :list="subcontratos"
                                                :placeholder="!cargando?'Seleccionar o buscar por número de folio o referencia de subcontrato':'Cargando...'"
                                                :isError="errors.has(`id_subcontrato`)">
                                            </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_subcontrato')">{{ errors.first('id_subcontrato') }}</div>
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
                                             <descarga-layout v-bind:id="id_subcontrato" v-if="id_subcontrato != ''" />
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
    import DatosSubcontrato from  "./partials/DatosSubcontrato";
    import DescargaLayout from "./DescargaLayout";
    export default {
        name: "estimacion-seleccionar-subcontrato",
        components: { DatosSubcontrato, ModelListSelect, DescargaLayout },
        data() {
            return {
                cargando: false,
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
            numeroFolioAndRefernciaAndEmpresa(item){
                return `[${item.numero_folio_format}] - [${item.referencia}]- [${item.empresa}]`
            },
            salir()
            {
                this.$router.push({name: 'estimacion'});
            },
            getSubcontratos(){
                this.subcontratos = [];
                this.cargando = true;
                return this.$store.dispatch('contratos/subcontrato/index', {
                    params:{ }
                }).then(data => {
                    this.subcontratos = data;
                }).finally(()=>{
                    this.cargando = false;
                })
            },
            find() {
                this.cargando = true;
                this.$store.commit('contratos/subcontrato/SET_SUBCONTRATO', null);
                return this.$store.dispatch('contratos/subcontrato/find', {
                    id: this.id_subcontrato,
                }).then(data => {
                    this.subcontrato = data;
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.$router.push({name: 'estimacion-create', params: {id: this.id_subcontrato}});
                    }
                });
            },
        },
        watch: {
            id_subcontrato(value)
            {
                if(value !== '' && value !== null && value !== undefined)
                {
                    this.find();
                }
            },
        }
    }
</script>

<style scoped>

</style>
