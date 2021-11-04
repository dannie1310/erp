<template>
    <span>
        <nav>
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <form role="form" @submit.prevent="validate">
                            <div class="modal-body">
                                <div class="row justify-content-between">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="id_contrato">Buscar Contrato Proyectado:</label>
                                                 <model-list-select
                                                     id="id_contrato"
                                                     name="id_contrato"
                                                     option-value="id"
                                                     v-model="id_contrato"
                                                     :custom-text="idFolioObservaciones"
                                                     :list="contratos"
                                                     :placeholder="!cargando?'Seleccionar o buscar folio o descripcion':'Cargando...'">
                                                 </model-list-select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('id_contrato')">{{ errors.first('id_contrato') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <DatosContratoProyectado :contrato_proyectado="contrato" v-if="contrato"></DatosContratoProyectado>
                            </div>

                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" v-on:click="salir">
                                        <i class="fa fa-angle-left"></i>
                                        Regresar</button>
                                    <button type="submit" :disabled="id_contrato == ''" class="btn btn-primary">
                                        Continuar
                                        <i class="fa fa-angle-right"></i>
                                    </button>
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
    import DatosContratoProyectado from "../proyectado/partials/DatosContratoProyectado";
    export default {
        name: "selecciona-contrato-proyectado-asignacion",
        components: {DatosContratoProyectado, ModelListSelect},
        data() {
            return {
                cargando: false,
                id_contrato: '',
                contratos : [],

            }
        },
        mounted() {
            this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
            this.$validator.reset();
            this.getContratos();

        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}] ---- [ ${item.referencia} ]`;
            },
            salir()
            {
                 this.$router.push({name: 'asignacion-contratista'});
            },
            find() {

                this.cargando = true;
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id_contrato,
                    params:{}
                }).then(data => {
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                    this.cargando = false;
                })
            },
            getContratos() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('contratos/contrato-proyectado/index', {
                    params: {
                        order: 'DESC',
                        sort: 'numero_folio',
                        scope:'conPresupuestos'
                    }
                })
                    .then(data => {
                        this.contratos = data.data;
                        this.cargando = false;
                    })
            },
            validate() {

                this.$validator.validate().then(result => {
                    if (result) {

                        this.$router.push({name: 'asignacion-contratista-create', params: {id_contrato: this.contrato.id}});
                    }

                });
            },
        },
        computed: {
            contrato(){
                return this.$store.getters['contratos/contrato-proyectado/currentContrato'];
            },
        },
        watch: {
            id_contrato(value)
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
