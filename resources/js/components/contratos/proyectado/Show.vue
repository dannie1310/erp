<template>
    <span>
        <div  v-if="!contrato">
            <div class="row" >
                <div class="col-md-12">
                    <div class="spinner-border text-success" role="status">
                       <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row col-md-12">
                        <div class="col-md-6">
                            <h5>Folio: &nbsp; <b>{{contrato.numero_folio_format}}</b></h5>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="bg-gray-light"><b>Fecha:</b></td>
                                    <td class="bg-gray-light"> {{contrato.fecha}} </td>
                                    <td class="bg-gray-light"><b>Área Subcontratante:</b></td>
                                    <td class="bg-gray-light">{{contrato.area_subcontratante}}</td>
                                    <td class="bg-gray-light"><b>Usuario Asignó:</b></td>
                                    <td class="bg-gray-light">{{contrato.usuario}}</td>
                                </tr>
                                <tr>
                                    <td class="bg-gray-light" align="center" colspan="6"><h6><b>{{contrato.referencia}}</b></h6></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6><b>Detalle de las partidas</b></h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th class="unidad">Unidad</th>
                                    </tr>
                                </thead>
                                <tbody v-if="contrato.conceptos">
                                <template v-for="(partida, i) in contrato.conceptos.data">
                                    <tr>
                                        <td style="text-align: left" v-html="partida.descripcion_formato"></td>
                                        <td style="text-align: center">{{partida.unidad}}</td>
                                    </tr>
                                    <tr>
                                        <td  colspan="2" style="color: #4f5962">{{partida.destino.path}}</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "contrato-proyectado-show",
        props: ['id'],
        data(){
            return{
                cargando: false
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {

                this.cargando = true;
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id,
                    params:{include: [
                        'conceptos.destino'
                    ]}
                }).then(data => {
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO', data);
                    this.cargando = false;
                })
            }
        },
        computed: {
            contrato() {
                return this.$store.getters['contratos/contrato-proyectado/currentContrato']
            },
        }
    }
</script>

<style scoped>

</style>
