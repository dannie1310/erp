<template>
    <span>
        <button @click="find(id)" type="button" class="btn btn-sm btn-outline-info" title="Editar"  :disabled="cargando">
            <i class="fa fa-pencil" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> EDITAR DESTAJISTA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="razon_social" class="col-md-2 col-form-label">Razón Social:</label>
                                        <div class="col-md-10">
                                            <input
                                                style="text-transform:uppercase;"
                                                type="text"
                                                name="razon_social"
                                                data-vv-as="Razón Social"
                                                v-validate="{required: true}"
                                                class="form-control"
                                                id="razon_social"
                                                placeholder="Razón Social"
                                                v-model="destajista.razon_social"
                                                :class="{'is-invalid': errors.has('razon_social')}">
                                            <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="rfc" class="col-md-4"><b>RFC: </b> </label>
                                        <div class="col-md-8">
                                            <input class="form-control"
                                                   style="text-transform:uppercase;"
                                                   name="rfc"
                                                   data-vv-as="RFC"
                                                   v-model="rfc"
                                                   :class="{'is-invalid':rfcValidate}"
                                                   v-validate="{ required: true }"
                                                   id="rfc"
                                                   placeholder="RFC" :maxlength="16"/>
                                            <span class="text-danger" v-if="rfcValidate">RFC Inválido</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row error-content">
                                        <label for="dias_credito" class="col-md-5 col-form-label">Condición de Pago (días):</label>
                                        <div class="col-md-7">
                                            <input
                                                type="number"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                name="dias_credito"
                                                data-vv-as="Condición de Pago (días)"
                                                v-validate="{ min_value: 0, max_value: 365 }"
                                                class="form-control"
                                                id="dias_credito"
                                                placeholder="Condición de Pago (días)"
                                                v-model="destajista.dias_credito"
                                                :class="{'is-invalid': errors.has('dias_credito')}">
                                            <div class="invalid-feedback" v-show="errors.has('dias_credito')">{{ errors.first('dias_credito') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" :disabled="errors.count() > 0" v-on:click="validate">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
    const rfcRegex =/^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
    export default {
        name: "destajista-edit",
        props: ['id'],
        data() {
            return {
                cargando: false,
                destajista : [],
                rfc : '',
                rfcValidate: false
            }
        },
        methods: {
            find() {
                return this.$store.dispatch('cadeco/destajista/find', {
                    id: this.id
                }).then(data => {
                    this.destajista = data;
                    $(this.$refs.modal).modal('show');
                }).finally(() => {
                    this.cargando = false;
                })
            },
            validate() {
                this.destajista.razon_social = this.destajista.razon_social.toUpperCase()
                this.$validator.validate().then(result => {
                    if (result && this.rfcValidate == false) {
                        this.update()
                    }
                });
            },
            update() {
                return this.$store.dispatch('cadeco/destajista/update', {
                    id: this.id,
                    data: this.destajista
                })
                    .then(data => {
                        this.$store.commit('cadeco/destajista/UPDATE_DESTAJISTA', data);
                        if(typeof data.efo !== 'undefined' && (data.efo.estado.id == 0 || data.efo.estado.id == 2)){
                            swal("El Destajista registrado es un "+data.efo.estado.descripcion+" EFO.", {
                                icon: "warning",
                                buttons: {
                                    confirm: {
                                        text: 'Enterado',
                                        closeModal: true,
                                    }
                                }
                            }) .then(() => {
                                $(this.$refs.modal).modal('hide');
                            })
                        }else {
                            $(this.$refs.modal).modal('hide');
                        }
                    })

            },
            invalidRFC(){
                this.rfcValidate=true;
            },
            validateRfc()
            {
                if(!rfcRegex.test(this.rfc)){
                    return this.invalidRFC();
                } else{
                    this.rfcValidate=false;
                    this.destajista.rfc = this.rfc
                    this.$validator.reset();
                }
            }
        },
        watch: {
            rfc(value) {
                this.rfc = this.rfc.toUpperCase();
                this.validateRfc();

            },
            destajista(value) {
                this.rfc = value.rfc;
            }
        }
    }
</script>

<style scoped>

</style>
