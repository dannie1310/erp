<template>
    <div class="card" id="configuracion-remesa" v-if="$root.can('editar_configuracion_finanzas_remesas')">
        <div class="card-body">
            <h5 id="remesa">Remesa</h5>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-4 pt-0"><b>¿El proyecto acepta documentos manuales en remesa?</b></legend>
                    <div class="col-sm-8" v-if="remesa">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="documentos_manuales1" value="1" v-model="remesa.documentos_manuales">
                            <label class="form-check-label"> Si</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="documentos_manuales0" value="0" v-model="remesa.documentos_manuales">
                            <label class="form-check-label"> No</label>
                        </div>
                    </div>
                    <div class="col-sm-8" v-else>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="documentos_manuales1" v-model="data.documentos_manuales" value="1">
                                Si</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <label class="form-check-label" style="cursor:pointer" >
                                <input class="form-check-input" type="radio" name="documentos_manuales0" v-model="data.documentos_manuales" value="0">
                                No</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="crear" class="btn btn-outline-primary pull-right" :disabled="!cambio" v-if="!remesa">
                        <i class="fa fa-save"></i>
                    </button>
                    <button type="submit" @click="validate" class="btn btn-outline-primary pull-right" v-else>
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [],
        name: "configuracion-remesa",
        data() {
            return {
                data:{
                    documentos_manuales:''
                },
                picked: 0,
            }
        },
        mounted() {
            this.find();
        },

        methods: {
            find() {
                this.$store.commit('seguridad/finanzas/configuracion-remesa/SET_REMESA', null);
                return this.$store.dispatch('seguridad/finanzas/configuracion-remesa/find').then(data => {
                    this.$store.commit('seguridad/finanzas/configuracion-remesa/SET_REMESA', data);
                })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.actualizar()
                    }
                });
            },
            crear() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/finanzas/configuracion-remesa/actualizar', {
                    data: this.data
                }).finally(() => {
                    this.guardando = false;
                })
            },
            actualizar() {
                this.guardando = true;
                return this.$store.dispatch('seguridad/finanzas/configuracion-remesa/actualizar', {
                    data: this.remesa
                }).finally(() => {
                        this.guardando = false;
                    })
            },
        },

        computed: {
            remesa() {
                return this.$store.getters['seguridad/finanzas/configuracion-remesa/currentRemesa'];
            },
            cambio() {
                return Boolean(this.data.documentos_manuales);
            }
        },
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .vue-treeselect__placeholder {
        color: #495057
    }
</style>