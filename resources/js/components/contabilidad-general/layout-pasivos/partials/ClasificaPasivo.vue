<template>
    <span>
        <button type="button" @click="inicia()" class="btn btn-sm btn-outline-primary" :disabled="actualizando"><i class="fa fa-boxes" title="Clasificar Pasivo sin CFDI"></i></button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-boxes"></i> Clasifica Pasivo sin CFDI</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- <form role="form" @submit.prevent="validate"> -->
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <model-list-select
                                                name="id_caso_sin_cfdi"
                                                placeholder="Seleccionar caso sin CFDI"
                                                data-vv-as="Caso sin CFDI"
                                                v-validate="{required: true}"
                                                v-model="id_caso_sin_cfdi"
                                                option-value="id"
                                                option-text="descripcion"
                                                :list="casos_sin_cfdi"
                                                :isError="errors.has(`id_caso_sin_cfdi`)">
                                            </model-list-select>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fa fa-times"></i>Cerrar</button>
                                    <button type="button" class="btn btn-primary" @click="clasifica()" >
                                        <i class="fa fa-arrow-down"></i>Clasificar</button>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    import {ModelListSelect} from 'vue-search-select';

    export default {
        name: "clasifica-pasivo",
        components : {ModelListSelect},
        props: ['pasivo_parametro'],
        data(){
            return {
                cargando : false,
                id_caso_sin_cfdi : null,
                casos_sin_cfdi : [],
            }
        },
        computed: {
            pasivo(){
                return this.$store.getters['contabilidadGeneral/layout-pasivo-partida/currentPasivo'];
            },
            actualizando() {
                return this.$store.getters['contabilidadGeneral/layout-pasivo/actualizando'];
            },
        },
        methods: {
            inicia(){
                this.$store.commit('contabilidadGeneral/layout-pasivo-partida/SET_PASIVO', this.pasivo_parametro);
                this.getCasosSinCFDI();
                this.$validator.reset();
                this.id_caso_sin_cfdi = this.pasivo.id_caso_sin_cfdi;
                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
            },
            getCasosSinCFDI(){
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo-partida/index_casos_sin_cfdi', {
                    params: {sort: 'descripcion',  order: 'asc'}
                })
                .then(data => {
                    this.casos_sin_cfdi = data.data;
                })
                .finally(() => {
                    this.cargando = false;
                })
            },
            clasifica(){
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo-partida/clasifica', {
                    id : this.pasivo_parametro.id,
                    params: {
                        id_caso_sin_cfdi : this.id_caso_sin_cfdi,
                    }
                })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo-partida/UPDATE_PASIVO', data);
                })
                .finally(() => {
                    this.cargando = false;
                    $(this.$refs.modal).modal('hide');
                })
            },
        },
    }
</script>

