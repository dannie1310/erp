<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-danger" title="Cancelar">
            <i class="fa fa-ban"></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-th"></i> CANCELAR CONTEO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" v-if="Conteo">
                                <div class="col-12">
                                    <div class="invoice p-3 mb-3">
                                        <div class="row">
                                            <div class="table-responsive col-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th>Folio:</th>
                                                            <td>{{Conteo.folio_marbete}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Conteo:</th>
                                                            <td>{{Conteo.tipo_conteo_format}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"  v-if="Conteo">
                                <div class="col-12">
                                <div class="invoice p-3 mb-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5>Datos de Conteo</h5>
                                        </div>
                                    </div>
                                    <form role="form">
                                        <div class="row">
                                            <div class="table-responsive col-md-12">
                                                <table class="table table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td><b>Cantidad Usados:</b></td>
                                                            <td>{{Conteo.cantidad_usados}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Cantidad Nuevos:</b></td>
                                                            <td>{{Conteo.cantidad_nuevo}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Cantidad Inservible:</b></td>
                                                            <td>{{Conteo.cantidad_inservible}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Total:</b></td>
                                                            <td>{{Conteo.total}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Iniciales:</b></td>
                                                            <td>{{Conteo.iniciales}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                         </div>
                         <div class="row" v-if="Conteo">
                            <div class="col-12">
                                <div class = "col-sm-6">
                                    <label>Observaciones:</label>
                                </div>
                                <div class = "col-sm-6">
                                      <h6>{{Conteo.observaciones}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11 form-group row error-content">
                                    <b>Motivo de Cancelación: </b>
                            </div>
                         </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="form-group row error-content">
                                    <div class="col-sm-11">
                                        <textarea
                                                name="observaciones"
                                                id="observaciones"
                                                class="form-control"
                                                v-model="observaciones"
                                                v-validate="{required: true}"
                                                data-vv-as="Observaciones"
                                                :class="{'is-invalid': errors.has('observaciones')}"
                                        ></textarea>
                                        <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Cancelar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "conteo-cancelar",
        props: ['id','pagina'],
        data() {
            return {
                observaciones: '',
                cargando : false
            }
        },
        methods: {
            find(id) {
                this.observaciones = '';
                this.$store.commit('almacenes/conteo/SET_CONTEO', null);
                return this.$store.dispatch('almacenes/conteo/find', {
                    id: id,
                    params: { include: ['marbete'] }
                }).then(data => {
                    this.$store.commit('almacenes/conteo/SET_CONTEO', data);
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.cancelar()
                    }
                });
            },
            cancelar() {
                return this.$store.dispatch('almacenes/conteo/cancelar', {
                    id: this.id,
                    params: { include: ['marbete'], data:[this.$data.observaciones]}
                }).then(data => {
                    this.$store.commit('almacenes/conteo/UPDATE_CONTEO', data)
                    $(this.$refs.modal).modal('hide');
                    this.cargando = true;
                    this.$store.dispatch('almacenes/conteo/paginate', { params:{
                            include: ['marbete.inventario_fisico'], sort: 'id_marbete', order: 'desc' ,limit:10, offset:this.pagina
                        }
                    })
                        .then(data => {
                            this.$store.commit('almacenes/conteo/SET_CONTEOS', data.data);
                            this.$store.commit('almacenes/conteo/SET_META', data.meta);
                        })
                        .finally(() => {
                            this.cargando = false;
                        })
                })
                    .finally( ()=>{
                        this.cargando = false;
                        this.observaciones = '';
                    });
            }
        },
        computed: {
            Conteo() {
                return this.$store.getters['almacenes/conteo/currentConteo'];
            },
            meta(){
                return this.$store.getters['almacenes/conteo/meta'];
            },
            tbodyStyle() {
                return this.cargando ?  { '-webkit-filter': 'blur(2px)' } : {}
            }
        }, watch: {
            cargando(val) {
                $('tbody').css({
                    '-webkit-filter': val ? 'blur(2px)' : '',
                    'pointer-events': val ? 'none' : ''
                });
            }
        }
    }
</script>

<style scoped>

</style>
