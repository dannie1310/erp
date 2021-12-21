<template>
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar" @click="init">
                <i class="fa fa-trash"></i>
            </button>

        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-trash"></i> Eliminar Empresa Boletinada</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label >RFC:</label>
                            </div>
                            <div class="col-md-8">
                                {{rfc}}
                            </div>
                        </div>
                        <br>
                       <div class="row">
                            <div class="col-md-4">
                                <label >Razón Social / Nombre:</label>
                            </div>
                            <div class="col-md-8">
                                {{razon_social}}
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label >Motivo:</label>
                            </div>
                            <div class="col-md-8">
                                {{motivo}}
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label >Observaciones:</label>
                            </div>
                            <div class="col-md-8">
                                {{observaciones}}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label >Motivo de Eliminación:</label>
                            </div>
                            <div class="col-md-8">
                                <textarea
                                    rows="2"
                                    id="motivo_eliminacion"
                                    name="motivo_eliminacion"
                                    data-vv-as="'Motivo de Eliminación'"
                                    class="form-control"
                                    v-validate="{ required: true}"
                                    :class="{'is-invalid': errors.has('motivo_eliminacion')}"
                                    v-model="motivo_eliminacion" />
                                <div class="invalid-feedback" v-show="errors.has('motivo_eliminacion')">{{ errors.first('motivo_eliminacion') }}</div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="submit" class="btn btn-danger" @click="validate" :disabled="errors.count() > 0" >
                            <i class="fa fa-trash"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DeleteEmpresaBoletinada",
    props: ['id'],
    data() {
        return {
            datos_store : {},
            cargando: false,
            observaciones : '',
            razon_social : '',
            rfc : '',
            motivo: '',
            motivo_eliminacion : '',
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
            this.find();
            this.motivo_eliminacion= '';
            this.$validator.reset();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        find()
        {
            this.cargando = true;
            return this.$store.dispatch('padronProveedores/empresa-boletinada/find', {
                id: this.id,
                params:{ include: []}
            }).then(data => {
                this.rfc = data.rfc;
                this.razon_social = data.razon_social;
                this.observaciones = data.observaciones;
                this.motivo  = data.motivo;
            }).finally(() => {
                this.cargando = false;
            })
        },
        salir() {
            $(this.$refs.modal).modal('hide');
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result){
                    this.delete()
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
        delete() {
            let _self = this;
            return this.$store.dispatch('padronProveedores/empresa-boletinada/delete', {
                id: this.id,
                params: {motivo_eliminacion: this.motivo_eliminacion}
            })
            .then((data) => {
                this.$store.commit('padronProveedores/empresa-boletinada/DELETE_EMPRESA', _self.id);
                $(this.$refs.modal).modal('hide');
            });
        }
    }
}
</script>

<style scoped>

</style>
