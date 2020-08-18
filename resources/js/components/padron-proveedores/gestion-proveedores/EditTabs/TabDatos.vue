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
                                    style="text-transform:uppercase;"
                                    v-model="empresa_registrar.razon_social"
                                    v-validate="{ required: true, min:6, max:255}"
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
                                   v-model="empresa_registrar.nss"
                                   v-validate="{ required: true, numeric:true, digits:11}"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="NSS" :maxlength="11"/>
                            <div class="invalid-feedback" v-show="errors.has('nss')">{{ errors.first('nss') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="contacto" class="col-form-label">Contacto:</label>
                            <input class="form-control"
                                   name="contacto"
                                   data-vv-as="CONTACTO"
                                   v-model="empresa_registrar.contacto"
                                   v-validate="{ required: true,min:10}"
                                   id="contacto"
                                   :class="{'is-invalid': errors.has('contacto')}"
                                   placeholder="CONTACTO"
                                   :maxlength="250"/>
                            <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="telefono" class="col-form-label">Teléfono:</label>
                            <input class="form-control"
                                   type="number"
                                   name="telefono"
                                   data-vv-as="TELÉFONO"
                                   v-model="empresa_registrar.telefono"
                                   v-validate="{ required: true, digits: 10}"
                                   id="telefono"
                                   :class="{'is-invalid': errors.has('telefono')}"
                                   placeholder="TELÉFONO" :maxlength="10"/>
                            <div class="invalid-feedback" v-show="errors.has('telefono')">{{ errors.first('telefono') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="correo" class="col-form-label">Correo:</label>
                            <input class="form-control"
                                   name="correo"
                                   data-vv-as="CORREO"
                                   v-model="empresa_registrar.correo"
                                   v-validate="{ required: true, email:true}"
                                   id="correo"
                                   :class="{'is-invalid': errors.has('correo')}"
                                   placeholder="CORREO"
                                   :maxlength="50"/>
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
                                v-model="empresa_registrar.giro.id"
                                option-value="id"
                                v-validate="{required: true}"
                                :custom-text="giroDescripcion"
                                size="4"
                                :list="giros"
                                :class="{'is-invalid': errors.has('giro')}">
                             </model-list-select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <br><br>
                            <input v-if="empresa_registrar.giro && empresa_registrar.giro.id == 'nuevo'"
                                   class="form-control"
                                   name="giro"
                                   data-vv-as="NUEVO GIRO"
                                   v-model="empresa_registrar.giro_nuevo"
                                   v-validate="{ required: true }"
                                   id="giro"
                                   :class="{'is-invalid': errors.has('giro')}"
                                   placeholder="AGREGAR UN GIRO NUEVO"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="especialidad" class="col-form-label">Especialidad:</label>
                            <treeselect v-model="especialidades_seleccionadas"
                                        :multiple="true"
                                        :options="especialidades"
                                        data-vv-as="ESPECIALIDADES"
                                        :flatten-search-results="true"
                                        placeholder="Selecciona la(s) especialidad(es)">
                                 <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                            </treeselect>
                            <div class="invalid-feedback" v-show="errors.has('especialidades_seleccionadas')">{{ errors.first('especialidades_seleccionadas') }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="autoSizingCheck" v-model="nueva_especialidad">
                                <label class="form-check-label" for="autoSizingCheck">Agregar una Especialidad Nueva...</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="nueva_especialidad">
                        <div class="form-group error-content">
                            <br><br>
                            <input class="form-control"
                                   name="especialidad"
                                   data-vv-as="NUEVA ESPECIALIDAD"
                                   v-model="empresa_registrar.especialidad_nuevo"
                                   v-validate="{ required: true, min: 5}"
                                   id="especialidad"
                                   :class="{'is-invalid': errors.has('especialidad')}"
                                   placeholder="AGREGAR UNA ESPECIALIDAD NUEVA"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="$root.can('editar_expediente_proveedor', true)"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    import Treeselect from '@riophae/vue-treeselect';
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "TabDatos",
        props: ['id'],
        components: {ModelListSelect, Treeselect},
        data() {
            return {
                especialidades_seleccionadas:[],
                empresa_registrar: [],
                giros: [],
                especialidades: [],
                nueva_especialidad : false,
                rfcValidate: false,
                cargando : true
            }
        },
        mounted(){
            this.getGiros();
            this.find();
        },
        methods: {
            especialidadesAcomodar () {
               this.especialidades = this.especialidades.map(i => ({
                    id: i.id,
                    label: `${i.descripcion}`,
                    customLabel: `${i.descripcion}`,
               }));
            },
            agregarEspecialidades()
            {
                if(this.empresa_registrar.especialidades) {
                    this.empresa_registrar.especialidades.data.forEach(e => {
                        this.especialidades_seleccionadas.push(e.id);
                    });
                }
            },
            giroDescripcion (item) {
                return `${item.descripcion}`
            },
            find() {
                return this.$store.dispatch('padronProveedores/empresa/find', {
                    id: this.id,
                    params: {include: ['tipo', 'giro', 'especialidades', 'prestadora']}
                }).then(data => {
                    this.empresa_registrar = data;
                    this.agregarEspecialidades();
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
                        this.especialidades = data.data;
                        this.especialidadesAcomodar();
                    }).finally(() => {
                        this.cargando = false;
                    })
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        if(this.especialidades_seleccionadas.length == 0 && this.nueva_especialidad == false)
                        {
                            swal('¡Error!', 'Debe existir al menos una especialidad seleccionada.', 'error')
                        }else {
                            this.update()
                        }
                    }
                });
            },
            update() {
                this.empresa_registrar.razon_social = this.empresa_registrar.razon_social.toUpperCase();
                this.empresa_registrar.rfc = this.empresa_registrar.rfc.toUpperCase();
                this.empresa_registrar.especialidades_nuevas = this.especialidades_seleccionadas;
                this.empresa_registrar.nueva_especialidad = this.empresa_registrar.nueva_especialidad;
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.id,
                    data: this.$data.empresa_registrar,
                    params: {include: ['prestadora', 'archivos', 'giro', 'especialidades']}
                }).then((data) => {
                    this.nueva_especialidad = false
                    this.especialidades_seleccionadas = [];
                    this.empresa_registrar = data
                    this.getEspecialidades();
                    this.agregarEspecialidades();
                    this.$store.commit('padronProveedores/empresa/SET_EMPRESA', data);
                })
            },
        }
    }
</script>

<style scoped>

</style>
