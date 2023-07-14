<template>
    <span>
        <button @click="show" type="button" class="btn btn-sm btn-outline-primary" title="Editar Pasivo">
            <i class="fa fa-pencil"></i>
        </button>
        <div class="modal fade" data-backdrop="static" data-keyboard="false" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> EDICIÓN DE PASIVOS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cleanData">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body" v-if="pasivo">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha:</label>

                                     <div class="form-group error-content">
                                            <datepicker v-model = "fecha"
                                                        :disabled="pasivo.coincide_fecha"
                                                        :value="fecha"
                                                        name = "fecha"
                                                        :language = "es"
                                                        :format = "formatoFecha"
                                                        :bootstrap-styling = "true"
                                                        class = "form-control"
                                                        v-validate="{required: true}"
                                                        :class="{'is-invalid': errors.has('fecha')}"
                                            ></datepicker>
                                         <div class="invalid-feedback" v-show="errors.has('fecha')">{{ errors.first('fecha') }}</div>
                                    </div>
                                        </div>
                                </div>

                            </div>
                            <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>RFC Proveedor:</label>

                                         <div class="form-group error-content">
                                             <input
                                                 :disabled="pasivo.coincide_rfc_proveedor"
                                                type="text"
                                                data-vv-as="RFC del Proveedor"
                                                v-validate="{required: true, max: 14}"
                                                class="form-control"
                                                name="rfc_proveedor"
                                                id="rfc_proveedor"
                                                placeholder="RFC Proveedor"
                                                v-model="rfc_proveedor"
                                                :class="{'is-invalid': errors.has('rfc_proveedor')}">
                                             <div class="invalid-feedback" v-show="errors.has('rfc_proveedor')">{{ errors.first('rfc_proveedor') }}</div>
                                         </div>
                                    </div>
                                 </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Folio:</label>

                                     <div class="form-group error-content">
                                        <input
                                            :disabled="pasivo.coincide_folio"
                                            type="text"
                                            data-vv-as="Folio de Factura"
                                            v-validate="{required: true, max: 14}"
                                            class="form-control"
                                            name="folio_factura"
                                            id="folio_factura"
                                            placeholder="Folio de Factura"
                                            v-model="folio_factura"
                                            :class="{'is-invalid': errors.has('folio_factura')}">
                                        <div class="invalid-feedback" v-show="errors.has('folio_factura')">{{ errors.first('folio_factura') }}</div>
                                    </div>
                                        </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Moneda:</label>

                                     <div class="form-group error-content">
                                        <input
                                            :disabled="pasivo.coincide_moneda"
                                            name="moneda"
                                            v-model="moneda"
                                            data-vv-as="Moneda"
                                            v-validate="{required: true, max: 10}"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has(`moneda`)}"
                                            id="moneda"
                                            placeholder="Moneda">
                                        <div class="invalid-feedback" v-show="errors.has('moneda')">{{ errors.first('moneda') }}</div>
                                    </div>
                                        </div>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group error-content">
                                        <label>Importe:</label>


                                        <input
                                            :disabled="pasivo.coincide_importe"
                                            name="importe_factura"
                                            v-model="importe_factura"
                                            data-vv-as="Importe de Factura"
                                            v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has(`importe_factura`)}"
                                            style="text-align: right"
                                            id="importe_factura"
                                            placeholder="Importe de Factura">
                                        <div class="invalid-feedback" v-show="errors.has('importe_factura')">{{ errors.first('importe_factura') }}</div>

                                        </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TC Factura:</label>
                                        <div class="form-group error-content">
                                        <input
                                            :disabled="pasivo.es_moneda_nacional"
                                            name="tc_factura"
                                            v-model="tc_factura"
                                            data-vv-as="TC Factura"
                                            v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has(`tc_factura`)}"
                                            id="tc_factura"
                                            style="text-align: right"
                                            placeholder="TC Factura">
                                        <div class="invalid-feedback" v-show="errors.has('tc_factura')">{{ errors.first('tc_factura') }}</div>
                                    </div>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Importe MXN:</label>
                                        <div >
                                    <input
                                        v-model="importe_mxn"
                                        class="form-control"
                                        style="text-align: right"
                                        disabled="disabled"
                                    >

                                </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Saldo:</label>

                                    <div class="form-group error-content">
                                        <input
                                            name="saldo"
                                            v-model="saldo"
                                            data-vv-as="Saldo de Factura"
                                            v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has(`saldo`)}"
                                            style="text-align: right"
                                            id="saldo"
                                            placeholder="Saldo de Factura">
                                        <div class="invalid-feedback" v-show="errors.has('saldo')">{{ errors.first('saldo') }}</div>
                                    </div>
                                        </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TC Saldo:</label>
                                        <div class="form-group error-content">
                                        <input
                                            :disabled="pasivo.es_moneda_nacional"
                                            name="tc_saldo"
                                            v-model="tc_saldo"
                                            data-vv-as="TC Saldo"
                                            v-validate="{required: true, regex: /^(\d|-)?(\d|,)*(\.\d{0,2})?$/}"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has(`tc_saldo`)}"
                                            id="tc_saldo"
                                            style="text-align: right"
                                            placeholder="TC Saldo">
                                        <div class="invalid-feedback" v-show="errors.has('tc_saldo')">{{ errors.first('tc_factura') }}</div>
                                    </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Saldo MXN:</label>
                                        <div >
                                    <input
                                        v-model="saldo_mxn"
                                        class="form-control"
                                        style="text-align: right"
                                        disabled="disabled"
                                    >

                                </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cleanData">
                                <i class="fa fa-times"/>Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 ">
                                <i class="fa fa-save" />Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>


<script>
    import Datepicker from 'vuejs-datepicker';
    import {es} from 'vuejs-datepicker/dist/locale'
    export default {
        name: "pasivos-edit",
        props: ['id','pasivo_parametro'],
        components: {Datepicker},
        data() {
            return {
                es: es,
                pasivo_store:{
                    fecha : '',
                    rfc_proveedor : '',
                    folio_factura : '',
                    importe_factura : '',
                    moneda : '',
                    tc_factura : '',
                    importe_mxn : '',
                    saldo : '',
                    tc_saldo : '',
                    saldo_mxn : '',
                },
                fecha : '',
                rfc_proveedor : '',
                folio_factura : '',
                importe_factura : '',
                moneda : '',
                tc_factura : '',
                importe_mxn : '',
                saldo : '',
                tc_saldo : '',
                saldo_mxn : '',
            }
        },
        computed: {
            pasivo(){
                return this.$store.getters['contabilidadGeneral/layout-pasivo/currentPasivo'];
            },
        },
        mounted(){

        },
        methods:{
            find()
            {
                let _self = this;
                if(this.pasivo == null && this.pasivo_parametro == null){
                    this.cargando = true;
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', null);
                    return this.$store.dispatch('contabilidadGeneral/layout-pasivo/findPasivo', {
                        id: this.id,
                        params: {
                        }
                    }).then(data => {
                        this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', data);
                    }).finally(() => {
                        this.cargando = false;
                    });
                }
                else if(this.id > 0 && this.id != this.pasivo.id) {
                    this.cargando = true;
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', null);
                    return this.$store.dispatch('contabilidadGeneral/layout-pasivo/find', {
                        id: this.id,
                        params: {
                            include: ['partidas'],
                            id: this.id
                        }
                    }).then(data => {
                        this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', data);
                    }).finally(() => {
                        this.cargando = false;
                    });
                } else if(this.pasivo_parametro != null){
                    this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', this.pasivo_parametro);
                    this.cargando = false;
                }
            },
            cleanData(){
                this.$store.commit('contabilidadGeneral/layout-pasivo/SET_PASIVO', null);
                this.pasivo_store={
                    fecha : '',
                    rfc_proveedor : '',
                    folio_factura : '',
                    importe_factura : '',
                    moneda : '',
                    tc_factura : '',
                    importe_mxn : '',
                    saldo : '',
                    tc_saldo : '',
                    saldo_mxn : '',
                };
            },
            formatoFecha(date){
                return moment(date).format('DD-MM-YYYY');
            },
            loadData(){
                var fecha = this.pasivo.fecha_factura.split('/');
                this.fecha = new Date(fecha[2], fecha[1]-1, fecha[0])
                this.rfc_proveedor = this.pasivo.rfc_proveedor;
                this.folio_factura = this.pasivo.folio_factura;
                this.importe_factura = this.pasivo.importe_factura;
                this.moneda = this.pasivo.moneda_factura;
                this.tc_factura = this.pasivo.tc_factura;
                this.importe_mxn = this.pasivo.importe_mxn;
                this.saldo = this.pasivo.saldo;
                this.tc_saldo = this.pasivo.tc_saldo;
                this.saldo_mxn = this.pasivo.saldo_mxn;
            },
            show() {
                this.find();
                this.loadData();
                $(this.$refs.modal).appendTo('body');
                $(this.$refs.modal).modal('show');
            },
            calcula_importe_mxn (){
                let importe_sin_comas;
                let tc_sin_comas;
                let importe_mxn = 0;
                importe_sin_comas = this.importe_factura.replace(/,/g, '');
                tc_sin_comas = this.tc_factura.replace(/,/g, '');
                importe_mxn = importe_sin_comas*tc_sin_comas;
                this.importe_mxn = parseFloat(importe_mxn).toString().formatearkeyUp();
            },

            calcula_saldo_mxn (){
                let saldo_sin_comas;
                let tc_saldo_sin_comas;
                let saldo_mxn = 0;
                saldo_sin_comas = this.saldo.replace(/,/g, '');
                tc_saldo_sin_comas = this.tc_saldo.replace(/,/g, '');
                saldo_mxn = saldo_sin_comas*tc_saldo_sin_comas;
                this.saldo_mxn = parseFloat(saldo_mxn).toString().formatearkeyUp();
            },

            validate() {
                this.$validator.detach("monto");
                this.$validator.validate().then(result => {
                    if (result){
                        this.update();
                    }
                });
            },

            update(){
                return this.$store.dispatch('contabilidadGeneral/layout-pasivo/updatePasivo', {
                    id: this.pasivo.id,
                    params: {},
                    data: this.pasivo_store
                })
                .then(data => {
                    this.$store.commit('contabilidadGeneral/layout-pasivo/UPDATE_PASIVO', data);
                    $(this.$refs.modal).modal('hide');
                })
            },
        },
        watch:{
            importe_factura(value){
                let cifra_formateada = 0;
                cifra_formateada = value.formatearkeyUp();
                this.importe_factura = cifra_formateada;
                this.calcula_importe_mxn();
            },
            tc_factura(value){
                let cifra_formateada = 0;
                cifra_formateada = value.formatearkeyUp();
                this.tc_factura = cifra_formateada;
                this.calcula_importe_mxn();
            },
            saldo(value){
                let cifra_formateada = 0;
                cifra_formateada = value.formatearkeyUp();
                this.saldo = cifra_formateada;
                this.calcula_saldo_mxn();
            },
            tc_saldo(value){
                let cifra_formateada = 0;
                cifra_formateada = value.formatearkeyUp();
                this.tc_saldo = cifra_formateada;
                this.calcula_saldo_mxn();
            },
        }

    }
</script>

<style scoped>

</style>
