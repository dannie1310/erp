<template>
    <div class="row">
        <div class="col-12">
            <button @click="init" type="button" class="btn btn-app float-right" >
                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                <i class="fa fa-plus" v-else></i>
                Registrar
            </button>

        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-plus"></i> Registrar empresa boletinada</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label >RFC:</label>
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-md-4">
                               <input class="form-control"
                                      name="rfc"
                                      data-vv-as="'RFC'"
                                      v-model="rfc"
                                      :class="{'is-invalid': errors.has('rfc')}"
                                      v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                      id="rfc"
                                      :maxlength="13"/>
                               <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                           </div>
                       </div>
                        <br>
                       <div class="row">
                            <div class="col-md-12">
                                <label >Razón Social / Nombre:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input
                                    name="razon_social"
                                    type="text"
                                    id="razon_social"
                                    data-vv-as="'Razón Social / Nombre'"
                                    class="form-control"
                                    v-validate="{ required: true}"
                                    :class="{'is-invalid': errors.has('razon_social')}"
                                    v-model="razon_social" />
                                <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label >Motivo:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group btn-group-toggle">
                                    <label class="btn btn-outline-secondary" :class="empresa_motivo_boletinada === key ? 'active': ''" v-for="(tipo_boletinada, key) in tipos_boletinada" :key="key">
                                        <input type="radio"
                                               class="btn-group-toggle"
                                               name="tipo_boletinada"
                                               :id="'tipo_boletinada' + key"
                                               :value="key"
                                               autocomplete="on"
                                               v-model="empresa_motivo_boletinada">
                                        {{ tipo_boletinada }}
                                    </label>
                                </div>

                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label >Observaciones:</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <textarea
                                rows="2"
                                id="observaciones"
                                name="observaciones"
                                data-vv-as="'Observaciones'"
                                class="form-control"
                                v-validate="{ required: true}"
                                :class="{'is-invalid': errors.has('observaciones')}"
                                v-model="observaciones" />
                                <div class="invalid-feedback" v-show="errors.has('observaciones')">{{ errors.first('observaciones') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" >
                            <i class="fa fa-save"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Create",
    data() {
        return {
            datos_store : {},
            cargando: false,
            observaciones : '',
            razon_social : '',
            rfc:'',
            empresa_motivo_boletinada : "1",
            tipos_boletinada: {
                1: "En Juicio",
                2: "Mala experiencia Operativa"
            },
            tipos_entidad: {
                1: "Empresa",
                2: "Representante Legal"
            },
        }
    },
    methods:{
        init() {
            this.empresa_motivo_boletinada = "1";
            this.rfc = '';
            this.razon_social = '';
            this.observaciones = '';
            this.$validator.reset();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        salir() {
            $(this.$refs.modal).modal('hide');
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.store()
                }else{
                    if(this.$refs.carga_layout.value !== ''){
                        this.$refs.carga_layout.value = '';
                        this.file = null;
                    }
                    this.$validator.errors.clear();
                    swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                }
            });
        },
        store() {
            this.datos_store["id_tipo_boletinadas"] = this.empresa_motivo_boletinada;
            this.datos_store["rfc"] = this.rfc;
            this.datos_store["razon_social"] = this.razon_social;
            this.datos_store["observaciones"] = this.observaciones;
            return this.$store.dispatch('padronProveedores/empresa-boletinada/store', this.datos_store)
                .then((data) => {
                    $(this.$refs.modal).modal('hide');
                    this.$emit('created', data);
                });
        }
    }
}
</script>

<style scoped>

</style>
