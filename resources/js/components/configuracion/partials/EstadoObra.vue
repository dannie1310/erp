<template>
    <div class="card" id="estado-obra" v-if="$root.can('actualizar_estado_obra', true)||$root.can('reactivar_obra',true)">
        <div class="card-header">
            <h6 class="card-title">Estado de Obra</h6>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" v-if="form">
            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-lg-2 pt-0"><b>Estado</b></legend>
                    <div class="col-lg-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra0" value="0" v-model="form.tipo_obra" :disabled="this.tipo_obra == 2 && !$root.can('reactivar_obra',true)">
                            <label class="form-check-label" for="tipo_obra0"> En ejecución </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra1" value="1" v-model="form.tipo_obra" :disabled="this.tipo_obra == 2 && !$root.can('reactivar_obra',true)">
                            <label class="form-check-label" for="tipo_obra1"> En Proyecto</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra3" value="3" v-model="form.tipo_obra" :disabled="this.tipo_obra == 2 && !$root.can('reactivar_obra',true)">
                            <label class="form-check-label" for="tipo_obra3"> Solo consulta</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra2" value="2" v-model="form.tipo_obra" :disabled="this.tipo_obra == 2 && !$root.can('reactivar_obra',true)">
                            <label class="form-check-label" for="tipo_obra2"> Terminada</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary float-right" :disabled="guardando || this.tipo_obra == 2 && !$root.can('reactivar_obra',true)">
                        <i class="fa fa-spin fa-spinner" v-if="guardando"></i>
                        <i class="fa fa-save" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import UsuarioSelect from "../../igh/usuario/Select";
    export default {
        name: "estado-obra",
        components: {UsuarioSelect},
        props: ['obra', 'tipo'],
        data() {
            return {
                user: '',
                logo: '',
                logo_nuevo: null,
                form: null,
                cargando: true,
                guardando: false,
                tipo_obra: 0
            }
        },

        mounted() {
            this.form = JSON.parse(JSON.stringify(this.obra));
            this.tipo_obra = this.obra.tipo_obra;
            setTimeout(() => {
                if (this.form.configuracion.logotipo_original) {
                    this.logo = `data:image/png;base64,${this.form.configuracion.logotipo_original}`;
                }
                if(this.form.configuracion.tipo_obra == 0 && this.form.configuracion.consulta == 1){
                    this.form.tipo_obra = 3;
                }
            }, 100);
        },

        methods: {
            onLogoSelected(event) {
                this.logo_nuevo = event.target.files[0]
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            update() {
                this.guardando = true;
                var formData = new FormData();


                if(this.form.tipo_obra == 3){
                    formData.append('configuracion[tipo_obra]', 0);
                    formData.append('tipo_obra', 0);
                    formData.append('configuracion[consulta]', 1);
                }else{
                    formData.append('configuracion[tipo_obra]', this.form.tipo_obra);
                    formData.append('tipo_obra', this.form.tipo_obra);
                    formData.append('configuracion[consulta]', 0);
                }
                formData.append('configuracion[base_datos]', this.form.configuracion.base_datos);
                formData.forEach((value, key) => {
                    if(value == 'null' || value == '')
                        formData.delete(key);
                });

                if(this.tipo == 0) {
                    return this.$store.dispatch('cadeco/obras/actualizarEstado', {
                        id: this.obra.id_obra,
                        data: formData,
                        config: {
                            params: {_method: 'PATCH', include: 'configuracion'}
                        }
                    })
                        .then(data => {
                            if (data) {
                                this.$store.commit('auth/setObra', {obra: data});
                                this.form = data
                                this.tipo_obra = this.obra.tipo_obra;
                                setTimeout(() => {
                                    if (data.configuracion.logotipo_original) {
                                        this.logo = `data:image/png;base64,${data.configuracion.logotipo_original}`;
                                    }
                                    if (this.form.configuracion.tipo_obra == 0 && this.form.configuracion.consulta == 1) {
                                        this.form.tipo_obra = 3;
                                    }
                                }, 100);
                            }
                        })
                        .finally(() => {
                            this.guardando = false;
                        })
                }
                if(this.tipo == 1) {
                    return this.$store.dispatch('cadeco/obras/actualizarEstadoGeneral', {
                        id: this.obra.id_obra,
                        data: formData,
                        config: {
                            params: {_method: 'PATCH'}
                        }
                    })
                        .then(data => {
                            if (data) {
                                this.form = data.obra
                                this.form.configuracion = data.configuracion;
                                this.tipo_obra = this.obra.tipo_obra;
                                setTimeout(() => {
                                    if (data.configuracion.logotipo_original) {
                                        this.logo = `data:image/png;base64,${data.configuracion.logotipo_original}`;
                                    }
                                    if (this.form.configuracion.tipo_obra == 0 && this.form.configuracion.consulta == 1) {
                                        this.form.tipo_obra = 3;
                                    }
                                }, 100);
                            }
                        })
                        .finally(() => {
                            this.guardando = false;
                        })
                }
            }
        },

        computed: {
            monedas() {
                return this.$store.getters['cadeco/moneda/monedas'];
            }
        }
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
