<template>
    <span>
        <button @click="find"  type="button" class="btn btn-sm btn-outline-secondary" title="Ver">
            <i class="fa fa-eye"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-sign-in"></i> AUTOCORRECCIÓN CFD DE EFO DEFINITIVO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body"  v-if="autocorreccion.length != 0">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Fecha: </label> {{autocorreccion.fecha}}
                                        </div>
                                        <div class="col-md-6">
                                            <label>Empresa EFOS: </label> {{autocorreccion.efo.razon_social}} ({{autocorreccion.efo.rfc}})
                                        </div>
                                        <div class="col-md-2">
                                            <span v-if="autocorreccion.estatus.id == 5" class="badge" :style="{'background-color': '#f39c12'}">{{ autocorreccion.estatus.descripcion }}</span>
                                            <span v-if="autocorreccion.estatus.id == 6" class="badge" :style="{'background-color': '#00a65a'}">{{ autocorreccion.estatus.descripcion }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card"  v-if="autocorreccion.partidas.length != 0">
                                <div class="card-body table-responsive">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>CFD</label>
                                        </div>
                                        <div class="col-md-6" align="right">
                                            <label>Total: {{parseFloat(autocorreccion.total_partidas).formatMoney(2,'.',',')}}</label>
                                        </div>
                                    </div>
                                    <div  class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="index_corto">#</th>
                                                <th>Folio</th>
                                                <th>UUID</th>
                                                <th>RFC de Receptor</th>
                                                <th>Razón Social de Receptor</th>
                                                <th>Serie</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(p, i) in autocorreccion.partidas.data">
                                                <td>{{i+1}}</td>
                                                <td>{{p.cfd.folio}}</td>
                                                <td>{{p.cfd.uuid}}</td>
                                                <td>{{p.cfd.rfc_receptor}}</td>
                                                <td>{{p.cfd.empresa.razon_social}}</td>
                                                <td>{{p.cfd.serie}}</td>
                                                <td>{{p.cfd.fecha_format}}</td>
                                                <td class="money">{{p.cfd.total_format}}</td>
                                                <td>{{p.estatus.descripcion}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary right" @click="salir">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "autocorreccion-cfd-efo-show",
        props: ['id'],
        data() {
            return{
                cargando : true,
                autocorreccion: [],
            }
        },
        methods: {
            find() {
                this.autocorreccion = [];
                this.cargando = true;
                return this.$store.dispatch('fiscal/autocorreccion/find', {
                    id: this.id,
                    params: {include:['efo', 'partidas.cfd.empresa']}
                }).then(data => {
                    this.autocorreccion = data;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                }).finally(() => {
                    this.cargando = false;
                })
            },
            salir(){
                $(this.$refs.modal).modal('hide');
            }
        }
    }
</script>

<style scoped>

</style>
