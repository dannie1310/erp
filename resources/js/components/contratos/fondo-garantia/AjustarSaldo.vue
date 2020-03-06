<template>
    <span>
        <button @click="init()" v-if="$root.can('ajustar_saldo_fondo_garantia')" type="button" class="btn btn-sm btn-outline-warning" title="Ajustar Saldo" :disabled="cargando" >
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-usd" style="padding:2px" v-else></i>
        </button>

        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" v-if="fondo_garantia">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-edit" style="padding-right:3px"></i>Ajustar Saldo de Fondo de Garantía del Subcontrato {{fondo_garantia.subcontrato.numero_folio_format}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-header" v-if="!fondo_garantia">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-edit" style="padding-right:3px"></i>Ajustar Saldo de Fondo de Garantía</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">

                             <div   class="card" v-if="fondo_garantia">
                                 <div class="card-header">
                                     <div class="row" >
                                         <div class="col-md-12">
                                              <label ><i  class="fa fa-info-circle" style="padding-right:3px"></i>Ingrese los datos solicitados</label>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="card-body">
                                    <div class="row">
                                         <div class="col-md-2">
                                            <label for="importe">Saldo Actual:</label>
                                        </div>
                                        <div class="col-md-2" >
                                            <span >{{fondo_garantia.saldo_format}}</span>

                                        </div>
                                        <!-- Ajuste -->
                                        <div class="col-md-2">
                                            <label for="importe">Ajuste:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group error-content">
                                                 <input
                                                         type="number"
                                                         step="any"
                                                         class="form-control"
                                                         id="importe"
                                                         name="importe"
                                                         v-model="importe"
                                                         v-validate="{required: true, is_not:0, decimal: true}"
                                                         data-vv-as="Ajuste"
                                                         :class="{'is-invalid': errors.has('importe')}"
                                                 >
                                                 <div class="invalid-feedback" v-show="errors.has('importe')">{{ errors.first('importe') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="importe">Saldo Resultante:</label>
                                        </div>
                                        <div class="col-md-2">
                                            {{saldo_resultante}}
                                        </div>
                                    </div>
                                     <div class="row">
                                        <!-- Motivo -->
                                        <div class="col-md-2">
                                            <label for="observaciones">Motivo:</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group error-content">
                                                <textarea
                                                        name="observaciones"
                                                        id="observaciones"
                                                        class="form-control"
                                                        v-model="observaciones"
                                                        v-validate="{required: true}"
                                                        data-vv-as="Motivo"
                                                        :class="{'is-invalid': errors.has('observaciones')}"
                                                ></textarea>
                                                 <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                             </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Ajustar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>

    export default {
        name: "fondo-garantia-ajustar-saldo",
        props: ['id'],
        data() {
            return {
                id_fondo_garantia : this.id,
                importe :0,
                saldo_resultante: 0,
                observaciones: '',
                fondo_garantia : null,
                cargando: false,
            }
        },

        mounted() {

        },

        methods: {
            find(payload) {
                this.cargando= true;
                return this.$store.dispatch('contratos/fondo-garantia/find', {
                    id: payload.id,
                    params: {include : 'subcontrato.empresa,subcontrato.moneda'}
                })
            },
            /*limpia_fondo(){
                return this.$store.dispatch('contratos/fondo-garantia/limpia')
            },*/
            init() {
                /*this.limpia_fondo();*/
                /*
                * this.find(id)
                    .then(data=>{
                        this.subcontrato = data
                        this.retencion = data.retencion
                        this.buscando = 0
                     })
                * */
                this.find({id: this.id}).then(data=>{
                    this.id_fondo_garantia=data.id
                    this.fondo_garantia = data
                    this.saldo_resultante= data.saldo
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
                    .finally(() => {
                        this.cargando = false;
                    });

                this.importe = 0;
                this.observaciones = '';
                this.$validator.reset()
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.store()
                    }
                });
            },
            store() {
                return this.$store.dispatch('contratos/fondo-garantia/ajustar_saldo', {
                    id:this.id,
                    data: this.$data,
                    params: {include : 'subcontrato.empresa,subcontrato.moneda'}
                })
                    .then((data) => {
                        this.$store.commit('contratos/fondo-garantia/UPDATE_FONDO_GARANTIA', data);
                        $(this.$refs.modal).modal('hide');
                    })
            },
        },

        computed: {
            /*fondo_garantia() { se usaba antes de quitar del find del store la actualizacoón del fondo de garantía acutal
                return this.$store.getters['contratos/fondo-garantia/currentFondoGarantia']
            }*/
        },

        watch : {
            importe ()
            {
                this.saldo_resultante = parseFloat(this.fondo_garantia.saldo) + parseFloat(this.importe);
            }
        }
    }
</script>

<style scoped>
    .modal-body {
        background-color: #efefef;
    }
    .btn-primary {
        background-color: #00c0ef;
        border-color: #00acd6;
        color: #FFF;
    }
    button:checked{
        background-color: #5bc0de;
    }
    .btn-primary:hover {
        background-color: #5bc0de;
        border-color: #46b8da;
    }
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .money
    {
        text-align: right;
    }
</style>
