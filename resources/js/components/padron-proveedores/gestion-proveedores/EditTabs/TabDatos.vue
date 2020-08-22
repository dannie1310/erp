<template>
    <span>
        <div class="card" v-if="!cargando">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
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
                    <div class="col-md-4">
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
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="giro" class="col-form-label">Giro:</label>
                            <model-list-select
                                id="id_giro"
                                name="id_giro"
                                placeholder="Seleccionar o buscar por descripcion de giro"
                                data-vv-as="Giro"
                                v-model="empresa_registrar.giro"
                                option-value="id"
                                option-text="descripcion"
                                v-validate="{required: true}"
                                :custom-text="giroDescripcion"
                                size="4"
                                :list="giros"
                                :class="{'is-invalid': errors.has('giro')}">
                             </model-list-select>
                            <div class="invalid-feedback" v-show="errors.has('id_giro')">{{ errors.first('id_giro') }}</div>
                            <input v-if="empresa_registrar.giro && empresa_registrar.giro.id == 'nuevo'"
                                   class="form-control"
                                   name="giro"
                                   data-vv-as="'Nuevo Giro'"
                                   v-model="empresa_registrar.giro_nuevo"
                                   v-validate="{ required: true }"
                                   id="giro"
                                   :class="{'is-invalid': errors.has('giro')}"
                                   placeholder="Ingresar Giro"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group error-content">
                            <label for="especialidad" class="col-form-label">Especialidad:</label>
                            <treeselect v-model="empresa_registrar.especialidades_nuevas"
                                        :multiple="true"
                                        :options="especialidades"
                                        data-vv-as="Especialidades"
                                        :flatten-search-results="true"
                                        placeholder="Selecciona la(s) especialidad(es)">
                                 <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                            </treeselect>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="autoSizingCheck" v-model="empresa_registrar.nueva_especialidad">
                                <label class="form-check-label" for="autoSizingCheck">Ingresar Especialidad</label>
                            </div>
                        </div>

                        <span v-if="empresa_registrar.nueva_especialidad">
                            <input class="form-control"
                                   name="especialidad"
                                   data-vv-as="'Nueva Especialidad'"
                                   v-model="empresa_registrar.especialidad_nuevo"
                                   v-validate="{ required: true, min: 5}"
                                   id="especialidad"
                                   :class="{'is-invalid': errors.has('especialidad')}"
                                   placeholder="Ingresar nueva especialidad"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </span>
                    </div>
                </div>

                <br>
                <div class="card">
                    <div class="card-header">
                        <label ><i class="fa fa-th-list icon"></i>Contactos</label>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="bg-gray-light index_corto">#</th>
                                    <th class="bg-gray-light">Nombre</th>
                                    <th class="bg-gray-light">Puesto</th>
                                    <th class="bg-gray-light">Teléfono</th>
                                    <th class="bg-gray-light">E-mail</th>
                                    <th class="bg-gray-light">Notas</th>
                                    <th class="bg-gray-light icono">
                                        <button type="button" class="btn btn-sm btn-outline-success" @click="agregarContacto" :disabled="cargando">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(contacto, i) in contactos.data" >
                                    <td class="index_corto">{{ i + 1 }}</td>
                                    <td>
                                        <input class="form-control"
                                               :name="`nombre[${i}]`"
                                               :data-vv-as="`'Nombre ${i + 1}'`"
                                               v-model="contacto.nombre"
                                               :class="{'is-invalid': errors.has(`nombre[${i}]`)}"
                                               v-validate="{ required: true, min:10 }"
                                               :id="`nombre[${i}]`"
                                               :maxlength="250"/>
                                        <div class="invalid-feedback" v-show="errors.has(`nombre[${i}]`)">{{ errors.first(`nombre[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               :name="`puesto[${i}]`"
                                               :data-vv-as="`'Puesto ${i + 1}'`"
                                               v-model="contacto.puesto"
                                               :class="{'is-invalid': errors.has(`puesto[${i}]`)}"
                                               v-validate="{ required: true, min:5 }"
                                               :id="`puesto[${i}]`"
                                               :maxlength="50"/>
                                        <div class="invalid-feedback" v-show="errors.has(`puesto[${i}]`)">{{ errors.first(`puesto[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               :name="`telefono[${i}]`"
                                               :data-vv-as="`'Teléfono ${i + 1}'`"
                                               v-model="contacto.telefono"
                                               :class="{'is-invalid': errors.has(`telefono[${i}]`)}"
                                               v-validate="{ required: true, numeric:true }"
                                               :id="`telefono[${i}]`"
                                               :maxlength="10"/>
                                        <div class="invalid-feedback" v-show="errors.has(`telefono[${i}]`)">{{ errors.first(`telefono[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               :name="`correo_electronico[${i}]`"
                                               :data-vv-as="`'correo_electronico ${i + 1}'`"
                                               v-model="contacto.correo_electronico"
                                               :class="{'is-invalid': errors.has(`correo_electronico[${i}]`)}"
                                               v-validate="{ required: true, email:true }"
                                               :id="`correo_electronico[${i}]`"
                                               :maxlength="50"/>
                                        <div class="invalid-feedback" v-show="errors.has(`correo_electronico[${i}]`)">{{ errors.first(`correo_electronico[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <textarea
                                            :name="`notas[${i}]`"
                                            :id="`notas[${i}]`"
                                            class="form-control"
                                            v-model="contacto.notas"
                                            :data-vv-as="`'Notas ${i + 1}'`"
                                        ></textarea>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarContacto(i)" :disabled="contactos.data.length == 1" >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0" @click="validate" v-if="$root.can('actualizar_expediente_proveedor', true)"><i class="fa fa-save"></i> Guardar</button>
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
                empresa_registrar: {
                    'id': '',
                    'rfc': '',
                    'razon_social': '',
                    'nss': '',
                    'giro': [],
                    'giro_nuevo':'',
                    'especialidades': [],
                    'nueva_especialidad' : false,
                    'especialidades_nuevas':[],
                    'especialidad_nuevo' : ''
                },
                contactos : [],
                giros: [],
                especialidades: [],
                cargando : true
            }
        },
        mounted(){
            this.getGiros();
            this.find();
        },
        methods: {
            agregarContacto(){
                var array = {
                    'nombre' : '',
                    'puesto' : '',
                    'telefono' : '',
                    'correo_electronico' : '',
                    'notas' : ''
                }
                this.contactos.data.push(array);
            },
            quitarContacto(index){
                this.contactos.data.splice(index, 1);
            },
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
                        this.empresa_registrar.especialidades_nuevas.push(e.id);
                    });
                }
            },
            find() {
                this.empresa_registrar.nueva_especialidad = false;
                this.empresa_registrar.especialidades_nuevas = [];
                return this.$store.dispatch('padronProveedores/empresa/find', {
                    id: this.id,
                    params: {include: ['giro', 'especialidades', 'contactos']}
                }).then(data => {
                    this.empresa_registrar.id = data.id;
                    this.empresa_registrar.rfc = data.rfc;
                    this.empresa_registrar.razon_social = data.razon_social;
                    this.empresa_registrar.nss = data.nss;
                    this.empresa_registrar.giro = data.giro;
                    this.empresa_registrar.especialidades = data.especialidades ? data.especialidades : [];
                    this.contactos = data.contactos ? data.contactos : [];
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
                        if(this.empresa_registrar.especialidades_nuevas.length == 0 && this.empresa_registrar.nueva_especialidad == false)
                        {
                            swal('¡Error!', 'Debe existir al menos una especialidad seleccionada.', 'error')
                        }
                        else if (this.contactos.data.length == 0)
                        {
                            swal('¡Error!', 'Debe existir al menos un contacto.', 'error')
                        }
                        else {
                            this.update()
                        }
                    }
                });
            },
            update() {
                this.empresa_registrar.razon_social = this.empresa_registrar.razon_social.toUpperCase();
                this.empresa_registrar.contactos = this.contactos;
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.id,
                    data: this.$data.empresa_registrar,
                    params: {include: ['prestadora', 'archivos']}
                }).then((data) => {
                    this.getEspecialidades();
                    this.getGiros();
                    this.$store.commit('padronProveedores/empresa/SET_EMPRESA', data);
                    this.find();
                })
            },
        }
    }
</script>

<style scoped>

</style>
