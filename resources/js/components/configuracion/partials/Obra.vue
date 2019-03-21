<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Configuración de Obra</h3>
        </div>
        <div class="card-body" v-if="form">
            <h5 id="identificacion">Identificaicón</h5>
            <div class="form-group row">
                <label for="logotipo_original" class="col-sm-2 col-form-label">Logotipo</label>
                <div :class="{'col-sm-5': logo, 'col-sm-10': !logo}">
                    <input type="file" class="form-control" id="logotipo_original" @change="onLogoSelected">
                </div>
                <div v-if="logo" class="thumbnail col-sm-5">
                    <img :src="logo" class="img-thumbnail">
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre" class="col-sm-2 col-form-label">Abreviatura</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre" v-model="form.nombre">
                </div>
            </div>

            <div class="form-group row">
                <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="descripcion" v-model="form.descripcion">
                </div>
            </div>

            <div class="form-group row">
                <label for="constructora" class="col-sm-2 col-form-label">Constructora</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="constructora" v-model="form.constructora">
                </div>
            </div>

            <div class="form-group row">
                <label for="cliente" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="cliente" v-model="form.cliente">
                </div>
            </div>

            <div class="form-group row">
                <label for="facturar" class="col-sm-2 col-form-label">Facturar a</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="facturar" v-model="form.facturar">
                </div>
            </div>

            <div class="form-group row">
                <label for="responsable" class="col-sm-2 col-form-label">Responsable</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="responsable" v-model="form.responsable">
                </div>
            </div>

            <div class="form-group row">
                <label for="rfc" class="col-sm-2 col-form-label">RFC</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="rfc" v-model="form.rfc">
                </div>
            </div>

            <hr>
            <h5 id="informacion_financiera">Información Financiera</h5>

            <div class="form-group row">
                <label for="id_moneda" class="col-sm-2 col-form-label">Moneda</label>
                <div class="col-sm-4">
                    <select class="form-control" id="id_moneda" v-model="form.id_moneda">
                        <option value>-- Moneda --</option>
                        <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                    </select>
                </div>
                <label for="iva" class="col-sm-2 col-form-label">Porcentaje de IVA</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="iva" v-model="form.iva">
                </div>
            </div>

            <div class="form-group row">
                <label for="valor_contrato" class="col-sm-2 col-form-label">Valor Contrato</label>
                <div class="col-sm-10">
                    <input type="number" step="any" class="form-control" id="valor_contrato" v-model="form.valor_contrato">
                </div>
            </div>

            <hr>
            <h5 id="plan">Plan de Obra</h5>

            <div class="form-group row">
                <label for="fecha_inicial" class="col-sm-2 col-form-label">Inicio de Obra</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="fecha_inicial" v-model="form.fecha_inicial">
                </div>
                <label for="fecha_final" class="col-sm-2 col-form-label">Fin de Obra</label>
                <div class="col-sm-4">
                    <input type="date" id="fecha_final" class="form-control" v-model="form.fecha_final">
                </div>
            </div>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Estado</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="0" value="0" v-model="form.tipo_obra">
                            <label class="form-check-label" for="0"> En ejecución </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="1" value="1" v-model="form.tipo_obra">
                            <label class="form-check-label" for="1"> En Proyecto</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="2" value="2" v-model="form.tipo_obra">
                            <label class="form-check-label" for="2"> Terminada</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <hr>
            <h5 id="seguridad">Seguridad</h5>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-sm-2 pt-0"><b>Esquema de Permisos</b></legend>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="1" value="1"
                                   v-model="form.configuracion.esquema_permisos"
                                   :disabled="!$root.can('modificar_esquema_permisos_proyecto')">
                            <label class="form-check-label" for="1"> Esquema Global</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="2" value="2"
                                   v-model="form.configuracion.esquema_permisos"
                                   :disabled="!$root.can('modificar_esquema_permisos_proyecto')">
                            <label class="form-check-label" for="2"> Esquema Personalizado</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <hr>
            <h5 id="ubicacion">Ubicación</h5>

            <div class="form-group row">
                <label for="direccion" class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <textarea id="direccion" class="form-control" v-model="form.direccion"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="ciudad" v-model="form.ciudad">
                </div>
            </div>
            <div class="form-group row">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="estado" v-model="form.estado">
                </div>
            </div>
            <div class="form-group row">
                <label for="codigo_postal" class="col-sm-2 col-form-label">Codigo Postal</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="codigo_postal" v-model="form.codigo_postal">
                </div>
            </div>

            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="update" class="btn btn-outline-primary pull-right" :disabled="guardando">
                        <i class="fa fa-spin fa-spinner" v-if="guardando"></i>
                        <i class="fa fa-save" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "configuracion-obra",
        props: ['obra'],
        data() {
            return {
                logo: null,
                logo_nuevo: null,
                form: null,
                cargando: true,
                guardando: false
            }
        },

        mounted() {
            this.form = JSON.parse(JSON.stringify(this.obra));
            setTimeout(() => {
                if (this.form.configuracion.logotipo_original) {
                    this.logo = "data:image/png;base64," + this.form.configuracion.logotipo_original;
                }
            }, 100);
        },

        methods: {
            onLogoSelected(event) {
                this.logo_nuevo = event.target.files[0]
            },

            update() {
                this.guardando = true;
                var formData = new FormData();
                if (this.logo_nuevo) {
                    formData.append('configuracion.logotipo_original', this.logo_nuevo, this.logo_nuevo.name);
                }

                formData.append('nombre', this.form.nombre);
                formData.append('descripcion', this.form.descripcion);
                formData.append('estado', this.form.estado);
                formData.append('direccion', this.form.direccion);
                formData.append('ciudad', this.form.ciudad);
                formData.append('codigo_postal', this.form.codigo_postal);
                formData.append('constructora', this.form.constructora)
                formData.append('cliente', this.form.cliente)
                formData.append('facturar', this.form.facturar)
                formData.append('responsable', this.form.responsable)
                formData.append('rfc', this.form.rfc)
                formData.append('id_moneda', this.form.id_moneda)
                formData.append('iva', this.form.iva)
                formData.append('fecha_inicial', this.form.fecha_inicial)
                formData.append('fecha_final', this.form.fecha_final)
                formData.append('tipo_obra', this.form.tipo_obra)
                if (this.form.valor_contrato)
                    formData.append('valor_contrato', this.form.valor_contrato)
                formData.append('configuracion.esquema_permisos', this.form.configuracion.esquema_permisos);

                return this.$store.dispatch('cadeco/obras/update', {
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
                                if (this.form.configuracion.logotipo_original) {
                                    this.logo = "data:image/png;base64," + this.form.configuracion.logotipo_original;
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