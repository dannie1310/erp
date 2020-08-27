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
                            <label for="nss" class="col-form-label">Registro Patronal:</label>
                            <input class="form-control"
                                   name="nss"
                                   data-vv-as="'Registro Patronal'"
                                   v-model="empresa_registrar.nss"
                                   v-validate="{ length:11, min:11 }"
                                   id="nss"
                                   :class="{'is-invalid': errors.has('nss')}"
                                   placeholder="Registro Patronal" :maxlength="11"/>
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
                <div class="card" v-if="empresa_registrar.tipo_empresa == 1">
                    <div class="card-header">
                        <label ><i class="fa fa-th-list icon"></i>Representantes Legales</label>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="bg-gray-light index_corto">#</th>
                                    <th class="bg-gray-light">Nombre(s)</th>
                                    <th class="bg-gray-light">Apellido Paterno</th>
                                    <th class="bg-gray-light">Apellido Materno</th>
                                    <th class="bg-gray-light">CURP</th>
                                    <th class="bg-gray-light icono">
                                        <button type="button" class="btn btn-sm btn-outline-success" @click="agregarRepresentanteLegal" :disabled="cargando">
                                            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                            <i class="fa fa-plus" v-else></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(representante_legal, i) in representantes_legales.data" >
                                    <td class="index_corto">{{ i + 1 }}</td>
                                    <td>
                                        <input class="form-control"
                                               :name="`nombre_rl[${i}]`"
                                               :data-vv-as="`'Nombre ${i + 1}'`"
                                               v-model="representante_legal.nombre"
                                               :class="{'is-invalid': errors.has(`nombre_rl[${i}]`)}"
                                               v-validate="{ required: true, min:3 }"
                                               :id="`nombre_rl[${i}]`"
                                               :maxlength="50"/>
                                        <div class="invalid-feedback" v-show="errors.has(`nombre_rl[${i}]`)">{{ errors.first(`nombre_rl[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               :name="`apellido_paterno[${i}]`"
                                               :data-vv-as="`'Apellido Paterno ${i + 1}'`"
                                               v-model="representante_legal.apellido_paterno"
                                               :class="{'is-invalid': errors.has(`apellido_paterno[${i}]`)}"
                                               v-validate="{ required: true, min:2 }"
                                               :id="`apellido_paterno[${i}]`"
                                               :maxlength="50"/>
                                        <div class="invalid-feedback" v-show="errors.has(`apellido_paterno[${i}]`)">{{ errors.first(`apellido_paterno[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <input class="form-control"
                                               :name="`apellido_materno[${i}]`"
                                               :data-vv-as="`'Apellido Materno ${i + 1}'`"
                                               v-model="representante_legal.apellido_materno"
                                               :class="{'is-invalid': errors.has(`apellido_materno[${i}]`)}"
                                               v-validate="{ required: true, min:2 }"
                                               :id="`apellido_materno[${i}]`"
                                               :maxlength="50"/>
                                        <div class="invalid-feedback" v-show="errors.has(`apellido_materno[${i}]`)">{{ errors.first(`apellido_materno[${i}]`) }}</div>
                                    </td>
                                    <td v-if="representante_legal.id">{{representante_legal.curp}}</td>
                                    <td v-else>
                                        <input class="form-control"
                                               :name="`curp[${i}]`"
                                               :data-vv-as="`'CURP ${i + 1}'`"
                                               v-model="representante_legal.curp"
                                               :class="{'is-invalid': errors.has(`curp[${i}]`)}"
                                               v-validate="{ required: true, min: 18, regex: /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/}"
                                               :id="`curp[${i}]`"
                                               :maxlength="18"/>
                                        <div class="invalid-feedback" v-show="errors.has(`curp[${i}]`)">{{ errors.first(`curp[${i}]`) }}</div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarRepresentanteLegal(i)" :disabled="representantes_legales.data.length == 1" >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    'especialidad_nuevo' : '',
                    'tipo_empresa' : ''
                },
                contactos : [],
                representantes_legales : [],
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
            agregarRepresentanteLegal(){
                var array = {
                    'nombre' : '',
                    'apellido_paterno' : '',
                    'apellido_materno' : '',
                    'curp' : ''
                }
                this.representantes_legales.data.push(array);
            },
            quitarRepresentanteLegal(index){
                this.representantes_legales.data.splice(index, 1);
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
                    params: {include: ['giro', 'especialidades', 'contactos', 'tipo', 'representantesLegales']}
                }).then(data => {
                    this.empresa_registrar.id = data.id;
                    this.empresa_registrar.rfc = data.rfc;
                    this.empresa_registrar.razon_social = data.razon_social;
                    this.empresa_registrar.nss = data.nss;
                    this.empresa_registrar.giro = data.giro;
                    this.empresa_registrar.especialidades = data.especialidades ? data.especialidades : [];
                    this.empresa_registrar.tipo_empresa = data.tipo.id;
                    this.contactos = data.contactos ? data.contactos : [];
                    this.representantes_legales = data.representantesLegales ? data.representantesLegales : [];
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
                        else if (this.representantes_legales.data.length == 0)
                        {
                            swal('¡Error!', 'Debe existir al menos un representante legal.', 'error')
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
                this.empresa_registrar.representantes_legales = this.representantes_legales;
                return this.$store.dispatch('padronProveedores/empresa/update', {
                    id: this.id,
                    data: this.$data.empresa_registrar,
                }).then((data) => {
                    location.reload();
                })
            },
        }
    }
</script>

<style scoped>

</style>
