<template>
    <span>
        <button type="button" @click="init()" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil" title="Editar"></i></button>
        <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-pencil"></i> {{empresa.nombre}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!-- <form role="form" @submit.prevent="validate"> -->
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group row error-content">
                                                    <label for="es_nacional" class="col-sm-6 col-form-label">Es Visible: </label>
                                                    <div class="col-sm-6">
                                                        <div class="btn-group btn-group-toggle">
                                                            <label class="btn btn-outline-secondary" :class="Visible === Number(1) ? 'active': ''"  :key="1">
                                                                <input type="radio" :disabled="!$root.can('configurar_visibilidad_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Visible"
                                                                    :id="'Visible' + 1"
                                                                    :value="1"
                                                                    autocomplete="on"
                                                                    v-model.number="Visible">
                                                                Si
                                                            </label>
                                                            <label class="btn btn-outline-secondary" :class="Visible === Number(0) ? 'active': ''"  :key="0">
                                                                <input type="radio" :disabled="!$root.can('configurar_visibilidad_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Visible"
                                                                    :id="'Visible' + 0"
                                                                    :value="0"
                                                                    autocomplete="on"
                                                                    v-model.number="Visible">
                                                                No
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-md-12">
                                            <div class="form-group row error-content">
                                                    <label for="es_nacional" class="col-sm-6 col-form-label">Es Editable: </label>
                                                    <div class="col-sm-6">
                                                        <div class="btn-group btn-group-toggle">
                                                            <label class="btn btn-outline-secondary" :class="Editable === Number(1) ? 'active': ''"  :key="1">
                                                                <input type="radio" :disabled="!$root.can('configurar_editabilidad_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Editable"
                                                                    :id="'Editable' + 1"
                                                                    :value="1"
                                                                    autocomplete="on"
                                                                    v-model.number="Editable">
                                                                Si
                                                            </label>
                                                            <label class="btn btn-outline-secondary" :class="Editable === Number(0) ? 'active': ''"  :key="0">
                                                                <input type="radio" :disabled="!$root.can('configurar_editabilidad_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Editable"
                                                                    :id="'Editable' + 0"
                                                                    :value="0"
                                                                    autocomplete="on"
                                                                    v-model.number="Editable">
                                                                No
                                                            </label>
                                                        </div>
                                                    </div>
                                             </div>
                                        </div>
                                        <div class="col-md-12" v-show="$root.can('configurar_tipo_empresa_ctpq', true)">
                                            <div class="form-group row error-content">
                                                    <label for="es_nacional" class="col-sm-6 col-form-label">Es Historica: </label>
                                                    <div class="col-sm-6">
                                                        <div class="btn-group btn-group-toggle">
                                                            <label class="btn btn-outline-secondary" :class="Historica === Number(1) ? 'active': ''"  :key="1">
                                                                <input type="radio" :disabled="!$root.can('configurar_tipo_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Historica"
                                                                    :id="'Historica' + 1"
                                                                    :value="1"
                                                                    autocomplete="on"
                                                                    v-model.number="Historica">
                                                                Si
                                                            </label>
                                                            <label class="btn btn-outline-secondary" :class="Historica === Number(0) ? 'active': ''"  :key="0">
                                                                <input type="radio" :disabled="!$root.can('configurar_tipo_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Historica"
                                                                    :id="'Historica' + 0"
                                                                    :value="0"
                                                                    autocomplete="on"
                                                                    v-model.number="Historica">
                                                                No
                                                            </label>
                                                        </div>
                                                    </div>
                                             </div>
                                        </div>
                                        <div class="col-md-12" v-show="$root.can('configurar_tipo_empresa_ctpq', true)">
                                            <div class="form-group row error-content">
                                                    <label for="es_nacional" class="col-sm-6 col-form-label">Es Consolidadora: </label>
                                                    <div class="col-sm-6">
                                                        <div class="btn-group btn-group-toggle">
                                                            <label class="btn btn-outline-secondary" :class="Consolidadora === Number(1) ? 'active': ''"  :key="1">
                                                                <input type="radio" :disabled="!$root.can('configurar_tipo_empresa_ctpq', true) && consolidada"
                                                                    class="btn-group-toggle"
                                                                    name="Consolidadora"
                                                                    :id="'Consolidadora' + 1"
                                                                    :value="1"
                                                                    autocomplete="on"
                                                                    v-model.number="Consolidadora">
                                                                Si
                                                            </label>
                                                            <label class="btn btn-outline-secondary" :class="Consolidadora === Number(0) ? 'active': ''"  :key="0">
                                                                <input type="radio" :disabled="!$root.can('configurar_tipo_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Consolidadora"
                                                                    :id="'Consolidadora' + 0"
                                                                    :value="0"
                                                                    autocomplete="on"
                                                                    v-model.number="Consolidadora">
                                                                No
                                                            </label>
                                                        </div>
                                                    </div>
                                             </div>
                                        </div>
                                        <div class="col-md-12" v-show="$root.can('configurar_es_desarrollo_empresa_ctpq', true)">
                                            <div class="form-group row error-content">
                                                    <label for="es_nacional" class="col-sm-6 col-form-label">Es Desarrollo: </label>
                                                    <div class="col-sm-6">
                                                        <div class="btn-group btn-group-toggle">
                                                            <label class="btn btn-outline-secondary" :class="Desarrollo === Number(1) ? 'active': ''"  :key="1">
                                                                <input type="radio" :disabled="!$root.can('configurar_es_desarrollo_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Desarrollo"
                                                                    :id="'Desarrollo' + 1"
                                                                    :value="1"
                                                                    autocomplete="on"
                                                                    v-model.number="Desarrollo">
                                                                Si
                                                            </label>
                                                            <label class="btn btn-outline-secondary" :class="Desarrollo === Number(0) ? 'active': ''"  :key="0">
                                                                <input type="radio" :disabled="!$root.can('configurar_es_desarrollo_empresa_ctpq', true)"
                                                                    class="btn-group-toggle"
                                                                    name="Desarrollo"
                                                                    :id="'Desarrollo' + 0"
                                                                    :value="0"
                                                                    autocomplete="on"
                                                                    v-model.number="Desarrollo">
                                                                No
                                                            </label>
                                                        </div>
                                                    </div>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-primary" @click="update()" v-if="$root.can('configurar_editabilidad_empresa_ctpq', true) || $root.can('configurar_visibilidad_empresa_ctpq', true)">Actualizar</button>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "lista-empresas-update",
    components: {},
    props: ['empresa', 'consolidada'],
    data() {
        return {
            Visible:'',
            Editable:'',
            Historica: '',
            Consolidadora: '',
            Desarrollo:'',
            cargando:false,
        }
    },
    mounted() {
    },
    methods: {
        init(){
            this.$validator.reset();
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
        },
        update(){
            if(this.consolidada == 1 && this.empresa.consolidadora != this.Consolidadora)
            {
                swal('¡Error!', 'La empresa no puede ser consolidadora porque ya se encuentra consolidada.', 'error')
            }else{

            return this.$store.dispatch('seguridad/lista-empresas/update', {
                    id: this.empresa.id,
                    params:{
                        Visible:parseInt(this.Visible),
                        Editable:parseInt(this.Editable),
                        Historica:parseInt(this.Historica),
                        Consolidadora:parseInt(this.Consolidadora),
                        Desarrollo: parseInt(this.Desarrollo)
                    }
                }).then(data => {
                                        
                    this.$store.commit('contabilidadGeneral/empresa/UPDATE_EMPRESA', data);
                    $(this.$refs.modal).modal('hide');
                })
            }
        },
    },
    watch: {
        empresa(value) {
            this.Editable = value.editable;
            this.Visible = value.visible;
            this.Historica = value.historica;
            this.Consolidadora = value.consolidadora;
            this.Desarrollo = value.desarrollo;
        }
    }

}
</script>

<style>

</style>
