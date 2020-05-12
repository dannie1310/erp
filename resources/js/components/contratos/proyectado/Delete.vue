<template>
    <span>
        <button @click="find" type="button" class="btn btn-sm btn-outline-danger " title="Eliminar" :disabled="cargando">
            <i class="fa fa-trash"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-eye"></i> ELIMINACIÓN DE CONTRATO PROYECTADO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="contrato">
                        <div class="row">
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
                                                        <th style="width:35%;">Destino</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="contrato.conceptos">
                                                    <tr v-for="(partida, i) in contrato.conceptos.data">
                                                        <td style="text-align: left" v-html="partida.descripcion_formato"></td>
                                                        <td style="text-align: center">{{partida.unidad}}</td>
                                                        <td style="text-align: center">{{(partida.destino) ? partida.destino.descripcion : null}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="!cargando">
                            <div class="col-md-12">
                                <div class="form-group row error-content">
                                    <label for="motivo" class="col-md-2 col-form-label">Motivo:</label>
                                    <div class="col-md-10">
                                        <textarea
                                            name="motivo"
                                            id="motivo"
                                            class="form-control"
                                            v-model="motivo"
                                            v-validate="{required: true}"
                                            data-vv-as="Motivo"
                                            :class="{'is-invalid': errors.has('motivo')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('motivo')">{{ errors.first('motivo') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''" v-on:click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "contrato-proyectado-delete",
        props: ['id'],
        data(){
            return{
                cargando: false,
                motivo: ''
            }
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO_PROYECTADOS', null);
                return this.$store.dispatch('contratos/contrato-proyectado/find', {
                    id: this.id,
                    params:{include: [
                            'conceptos.destino'
                        ]}
                }).then(data => {
                    this.$store.commit('contratos/contrato-proyectado/SET_CONTRATO_PROYECTADOS', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show')
                    this.cargando = false;
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.motivo == '') {
                            swal('¡Error!', 'Debe colocar un motivo para realizar la operación.', 'error')
                        }
                        else {
                            this.eliminar()
                        }
                    }
                });
            },
            eliminar() {
                return this.$store.dispatch('contratos/contrato-proyectado/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.$router.push({name: 'contrato-proyectado'});
                    })
            },
        },
        computed: {
            contrato() {
                return this.$store.getters['contratos/contrato-proyectado/currentContratos']
            },
        }
    }
</script>

<style scoped>

</style>
