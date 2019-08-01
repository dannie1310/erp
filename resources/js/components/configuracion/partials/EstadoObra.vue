<template>
    <div class="card" id="estado-obra" v-if="$root.can('actualizar_estado_obra')">
        <div class="card-header">
            <h3 class="card-title">Estado de Obra</h3>
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
                            <input class="form-check-input" type="radio" id="tipo_obra0" value="0" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra0"> En ejecución </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra1" value="1" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra1"> En Proyecto</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra3" value="3" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra3"> Solo consulta</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra2" value="2" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra2"> Terminada</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary pull-right" :disabled="guardando">
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
        props: ['obra'],
        data() {
            return {
                user: '',
                logo: '',
                logo_nuevo: null,
                form: null,
                cargando: true,
                guardando: false,
                tipos_proyecto: []
            }
        },

        mounted() {
            this.getTiposProyectos();
            this.form = JSON.parse(JSON.stringify(this.obra));
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
            getTiposProyectos() {
                this.cargando = true;
                return this.$store.dispatch('seguridad/tipo-proyecto/index')
                    .then(data => {
                        this.tipos_proyecto = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

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

                formData.forEach((value, key) => {
                    if(value == 'null' || value == '')
                        formData.delete(key);
                });

                return this.$store.dispatch('cadeco/obras/actualizarEstado', {
                    id: this.obra.id_obra,
                    data: formData,
                    config: {
                        params: { _method: 'PATCH', include: 'configuracion'}
                    }
                })
                    .then(data => {
                        if (data) {
                            this.$store.commit('auth/setObra', { obra: data });
                            this.form = data
                            setTimeout(() => {
                                if (data.configuracion.logotipo_original) {
                                    this.logo = `data:image/png;base64,${data.configuracion.logotipo_original}`;
                                }
                                if(this.form.configuracion.tipo_obra == 0 && this.form.configuracion.consulta == 1){
                                    this.form.tipo_obra = 3;
                                }
                            }, 100);
                        }
                    })
                    .finally(() => {
                        this.guardando = false;
                    })
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