<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="id_contrato">Buscar Contrato Proyectado:</label>
                         <model-list-select
                             id="id_contrato"
                             name="id_contrato"
                             option-value="id"
                             v-model="id_contrato"
                             :custom-text="idFolioObservaciones"
                             :list="contratos_proyectados"
                             :placeholder="!cargando?'Seleccionar o buscar folio o referencia':'Cargando...'">
                         </model-list-select>
                    <div style="display:block" class="invalid-feedback" v-show="errors.has('id_contrato')">{{ errors.first('id_contrato') }}</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <DatosContratoProyectado :contrato_proyectado="contrato_proyectado"></DatosContratoProyectado>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import DatosContratoProyectado from "./DatosContratoProyectado";
    export default {
        name: "select-contrato-proyectado",
        components: {
            DatosContratoProyectado,
            ModelListSelect},
        data() {
            return {
                cargando: false,
                id_contrato: '',
                contratos_proyectados : [],
            }
        },
        mounted() {
            //this.$store.commit('compras/solicitud-compra/SET_SOLICITUD', null);

            this.getContratosProyectados();

        },
        methods : {
            idFolioObservaciones (item)
            {
                return `[${item.numero_folio_format}]-[${item.referencia}]`;
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
            getContratosProyectados() {
                this.solicitudes = [];
                this.cargando = true;
                return this.$store.dispatch('contratos/contrato-proyectado/index', {
                    params: {
                        scope: ['conItems','areasContratantesAsignadas'],
                        order: 'DESC',
                        sort: 'numero_folio'
                    }
                })
                    .then(data => {
                        this.contratos_proyectados = data.data;
                    })
                    .finally(()=>{
                        this.cargando = false;
                    })
            },
        },
        computed: {
            contrato_proyectado(){
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
