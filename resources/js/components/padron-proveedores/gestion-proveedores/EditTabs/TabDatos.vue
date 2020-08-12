<template>
    <span>
        <div class="card" v-if="!cargando">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group error-content">
                            <label for="razon" class="col-form-label">Razón Social:</label>
                             <input class="form-control"
                                    name="razon"
                                    data-vv-as="RAZÓN SOCIAL"
                                    v-model="empresa.razon_social"
                                    v-validate="{ required: true}"
                                    id="razon"
                                    :class="{'is-invalid': errors.has('razon')}"
                                    placeholder="RAZÓN SOCIAL" :maxlength="255"/>
                            <div class="invalid-feedback" v-show="errors.has('razon')">{{ errors.first('razon') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="nss" class="col-form-label">NSS:</label>
                            <input class="form-control"
                                   name="nss"
                                   data-vv-as="NSS"
                                   v-model="empresa.nss"
                                   v-validate="{ required: true }"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="NSS"/>
                            <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="contacto" class="col-form-label">Contacto:</label>
                            <input class="form-control"
                                   name="contacto"
                                   data-vv-as="CONTACTO"
                                   v-model="empresa.contacto"
                                   v-validate="{ required: true }"
                                   id="contacto"
                                   :class="{'is-invalid': errors.has('contacto')}"
                                   placeholder="CONTACTO"/>
                            <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="telefono" class="col-form-label">Teléfono:</label>
                            <input class="form-control"
                                   name="telefono"
                                   data-vv-as="TELÉFONO"
                                   v-model="empresa.telefono"
                                   v-validate="{ required: true }"
                                   id="telefono"
                                   :class="{'is-invalid': errors.has('telefono')}"
                                   placeholder="TELÉFONO"/>
                            <div class="invalid-feedback" v-show="errors.has('telefono')">{{ errors.first('telefono') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="correo" class="col-form-label">Correo:</label>
                            <input class="form-control"
                                   name="correo"
                                   data-vv-as="CORREO"
                                   v-model="empresa.correo"
                                   v-validate="{ required: true }"
                                   id="correo"
                                   :class="{'is-invalid': errors.has('correo')}"
                                   placeholder="CORREO"/>
                            <div class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="giro" class="col-form-label">Giro:</label>
                            <model-list-select
                                name="giro"
                                placeholder="Seleccionar o buscar por descripcion de giro"
                                data-vv-as="Giro"
                                v-model="empresa.giro.id"
                                option-value="id"
                                v-validate="{required: true}"
                                :custom-text="giroDescripcion"
                                :list="giros"
                                :class="{'is-invalid': errors.has('giro')}">
                             </model-list-select>
                            <br>
                            <input v-if="empresa.giro && empresa.giro.id == 'nuevo'"
                                   class="form-control"
                                   name="giro"
                                   data-vv-as="GIRO"
                                   v-model="empresa.giro_nuevo"
                                   v-validate="{ required: true }"
                                   id="giro"
                                   :class="{'is-invalid': errors.has('giro')}"
                                   placeholder="GIRO"/>
                            <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="especialidad" class="col-form-label">Especialidad:</label>
                             <model-list-select
                                 name="especialidad"
                                 placeholder="Seleccionar o buscar por descripcion de especialidad"
                                 data-vv-as="Especialidad"
                                 v-model="empresa.especialidad.id"
                                 option-value="id"
                                 v-validate="{required: true}"
                                 :custom-text="especialidadDescripcion"
                                 :list="especialidades"
                                 :class="{'is-invalid': errors.has('especialidad')}">
                             </model-list-select>
                            <br>
                            <input v-if="empresa.especialidad && empresa.especialidad.id == 'nuevo'"
                                   class="form-control"
                                   name="especialidad"
                                   data-vv-as="ESPECIALIDAD"
                                   v-model="empresa.especialidad_nuevo"
                                   v-validate="{ required: true }"
                                   id="especialidad"
                                   :class="{'is-invalid': errors.has('especialidad')}"
                                   placeholder="ESPECIALIDAD"/>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" :disabled="errors.count() > 0" @click="validate">Guardar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "TabDatos",
        props: ['id'],
        components: {ModelListSelect},
        data() {
            return {
                empresa: [],
                giros: [],
                especialidades: [],
                rfcValidate: false,
                cargando : true
            }
        },
        mounted(){
            this.getGiros();
            this.find();
        },
        methods: {
            especialidadDescripcion (item) {
                return `${item.descripcion}`
            },
            giroDescripcion (item) {
                return `${item.descripcion}`
            },
            find() {
                return this.$store.dispatch('padronProveedores/empresa/find', {
                    id: this.id,
                    params: {include: ['tipo', 'giro', 'especialidad', 'prestadora', 'archivos']}
                }).then(data => {
                    this.empresa = data;
                })
            },
            getGiros() {
                return this.$store.dispatch('padronProveedores/giro/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        data.data.push({
                            'descripcion': 'Agregar...',
                            'id': 'nuevo'
                        });
                        this.giros = data.data;
                    }).finally(() => {
                        this.getEspecialidades();
                    })
            },
            getEspecialidades() {
                this.cargando = true
                return this.$store.dispatch('padronProveedores/especialidad/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                    .then(data => {
                        data.data.push({
                            'descripcion': 'Agregar...',
                            'id': 'nuevo'
                        });
                        this.especialidades = data.data;
                    }).finally(() => {
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },
            update() {
                this.empresa.razon_social = this.empresa.razon_social.toUpperCase();
                this.empresa.rfc = this.empresa.rfc.toUpperCase();
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.id,
                    data: this.$data.empresa
                }).then((data) => {
                    this.$store.commit('padronProveedores/empresa/SET_EMPRESA', data);
                })
            },
        }
    }
</script>

<style scoped>

</style>
