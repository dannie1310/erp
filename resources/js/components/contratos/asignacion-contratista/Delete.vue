<template>
    <span>
        <div class="row" v-if="!asignacion">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="card" v-if="asignacion">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td class="bg-gray-light"><b>Folio Asignación:</b></td>
                                <td class="bg-gray-light">{{asignacion.numero_folio}}</td>
                                <td class="bg-gray-light"><b>Fecha Registro:</b></td>
                                <td class="bg-gray-light">{{asignacion.fecha_format}}</td>
                                <td class="bg-gray-light"><b>Registro:</b></td>
                                <td class="bg-gray-light">{{asignacion.usuario}}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray-light"><b>Folio Contrato:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.numero_folio_format}}</td>
                                <td class="bg-gray-light"><b>Fecha:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.fecha}}</td>
                                <td class="bg-gray-light"><b>Referencia:</b></td>
                                <td class="bg-gray-light">{{asignacion.contrato.referencia}}</td>
                            </tr>
                            <!-- <tr>
                                <td class="bg-gray-light"><b>Contrato:</b></td>
                                <td class="bg-gray-light">{{subcontratos.contrato_folio_format}}</td>
                            </tr> -->

                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="card" v-if="asignacion.partidas">
                    <div class="card-header">
                        <h6><b>Detalle de las partidas</b></h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(doc, i) in asignacion.partidas.data">
                                        <td>{{i+1}}</td>
                                        <td align="center">{{doc.concepto.descripcion}}</td>
                                        <td>{{doc.concepto.unidad}}</td>
                                        <td class="td_money">{{doc.cantidad_asignada}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Motivo: </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row error-content">
                            <div class="col-md-12">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" v-on:click="salir">Cerrar</button>
                        <button type="button" class="btn btn-danger" :disabled="errors.count() > 0 || motivo == ''" v-on:click="validate">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "asignacion-proveedor-delete",
        props: ['id'],
        data() {
            return {
                motivo: ''
            }
        },
        mounted() {
            this.find();
        },
        methods: {
            find() {
                this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', null);
                return this.$store.dispatch('contratos/asignacion-contratista/find', {
                    id: this.id,
                    params: {include: ['contrato', 'partidas.concepto']}
                }).then(data => {
                    this.$store.commit('contratos/asignacion-contratista/SET_ASIGNACION', data);
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
                return this.$store.dispatch('contratos/asignacion-contratista/eliminar', {
                    id: this.id,
                    params: {data: this.$data.motivo}
                })
                    .then(data => {
                        this.$router.push({name: 'asignacion-contratista'});
                    })
            },
            salir(){
                this.$router.push({name: 'asignacion-contratista'});
            },
        },
        computed: {
            asignacion() {
                return this.$store.getters['contratos/asignacion-contratista/currentAsignacion'];
            }
        },
    }
</script>

<style scoped>

</style>
