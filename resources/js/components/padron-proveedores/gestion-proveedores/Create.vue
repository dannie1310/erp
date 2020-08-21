<template>
    <span>
        <div class="card">
            <div class="card-header">
                <label ><i class="fa fa-th-list icon"></i>Datos Generales del Proveedor</label>
            </div>
            <div class="card-body">
                 <div class="form-row">
                     <div class="form-group col-md-12 error-content">
                         <label for="razon_social" class="col-form-label">Razón Social:</label>
                         <input
                                style="text-transform:uppercase;"
                                type="text"
                                name="razon_social"
                                data-vv-as="'Razón Social'"
                                v-validate="{required: true, min:6}"
                                class="form-control"
                                id="razon_social"
                                v-model="registro_proveedor.razon_social"
                                :class="{'is-invalid': errors.has('razon_social')}" />
                         <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                     </div>
                 </div>
                <div class="form-row">
                    <div class="form-group col-md-3 error-content">
                        <label for="rfc" class="col-form-label" ><b>RFC: </b> </label>
                         <input class="form-control"
                                name="rfc"
                                data-vv-as="'RFC'"
                                v-model="registro_proveedor.rfc"
                                :class="{'is-invalid': errors.has('rfc')}"
                                v-validate="{ required: true, regex: /^([A-ZÑ&]{3,4})(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]))([A-Z\d]{2})([A\d])$/ }"
                                id="rfc"
                                :maxlength="13"/>
                        <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                    </div>
                    <div class="form-group col-md-3 error-content">
                        <label for="rfc" class="col-form-label" ><b>NSS: </b> </label>
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="numero_imss"
                               data-vv-as="'NSS'"
                               v-model="registro_proveedor.no_imss"
                               :class="{'is-invalid': errors.has('numero_imss')}"
                               v-validate="{ required: true, numeric:true, min:11 }"
                               id="numero_imss"
                               :maxlength="11"/>
                        <div class="invalid-feedback" v-show="errors.has('numero_imss')">{{ errors.first('numero_imss') }}</div>
                    </div>
                    <div class="form-group col-md-3 error-content">
                        <label for="rfc" class="col-form-label" ><b>Giro: </b> </label>
                        <span v-if="giros.length>0">
                            <model-list-select
                                ref="lista_giros"
                                :disabled="cargando"
                                id="id_giro"
                                name="id_giro"
                                placeholder="Seleccionar o buscar giro"
                                data-vv-as="'Giro'"
                                v-model="registro_proveedor.id_giro"
                                option-value="id"
                                option-text="descripcion"
                                v-validate="{ required: true }"
                                :list="giros"
                                :onchange="changeSelectGiro()"
                                :isError="errors.has(`id_giro`)">
                        >
                            </model-list-select>
                            <div class="invalid-feedback" v-show="errors.has('id_giro')">{{ errors.first('id_giro') }}</div>
                            <span v-if="agregar_giro">
                                <input class="form-control"
                                       name="otro_giro"
                                       data-vv-as="'Giro'"
                                       v-model="registro_proveedor.giro"
                                       :class="{'is-invalid': errors.has('otro_giro')}"
                                       v-validate="{ required: true }"
                                       id="otro_giro"
                                       placeholder="Ingresar Giro"
                                       :maxlength="50"/>
                                <div class="invalid-feedback" v-show="errors.has('otro_giro')">{{ errors.first('otro_giro') }}</div>
                            </span>
                        </span>
                        <span v-else>
                            <input class="form-control"
                                   name="giro"
                                   data-vv-as="'Giro'"
                                   v-model="registro_proveedor.giro"
                                   v-validate="{ required: true }"
                                   :class="{'is-invalid': errors.has('giro')}"
                                   id="giro"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                        </span>
                    </div>
                    <div class="form-group col-md-3 error-content">
                        <label for="rfc" class="col-form-label" ><b>Especialidad: </b> </label>
                        <span v-if="especialidades.length>0">
                            <treeselect v-model="registro_proveedor.id_especialidades"
                                        :multiple="true"
                                        :options="especialidades"
                                        data-vv-as="Especialidades"
                                        :flatten-search-results="true"
                                        placeholder="Seleccione la(s) especialidad(es)">
                                 <div slot="value-label" slot-scope="{ node }">{{ node.raw.customLabel }}</div>
                            </treeselect>
                            <div class="invalid-feedback" v-show="errors.has('registro_proveedor.id_especialidades')">{{ errors.first('registro_proveedor.id_especialidades') }}</div>

                            <div class="col-auto">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoSizingCheck" v-model="agregar_especialidad">
                                    <label class="form-check-label" for="autoSizingCheck">Agregar una especialidad nueva...</label>
                                </div>
                            </div>


                            <span v-if="agregar_especialidad">
                                <input class="form-control"
                                       name="otra_especialidad"
                                       data-vv-as="'Especialidad'"
                                       v-model="registro_proveedor.especialidad"
                                       :class="{'is-invalid': errors.has('otra_especialidad')}"
                                       id="otra_especialidad"
                                       v-validate="{required:true}"
                                       placeholder="Ingresar Especialidad"
                                       :maxlength="50"/>
                                <div class="invalid-feedback" v-show="errors.has('otra_especialidad')">{{ errors.first('otra_especialidad') }}</div>
                            </span>
                        </span>
                        <span v-else>
                            <input class="form-control"
                                   name="especialidad"
                                   data-vv-as="'Especialidad'"
                                   v-model="registro_proveedor.especialidad"
                                   :class="{'is-invalid': errors.has('especialidad')}"
                                   v-validate="{ required: true }"
                                   id="especialidad"
                                   :maxlength="50"/>
                            <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <label ><i class="fa fa-th-list icon"></i>Contactos</label>
            </div>
            <div class="card-body table-responsive">
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
                        <tr v-for="(contacto, i) in registro_proveedor.contactos" >
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

                            <td >
                                <input class="form-control"
                                       :name="`telefono[${i}]`"
                                       :data-vv-as="`'Teléfono ${i + 1}'`"
                                       v-model="contacto.telefono"
                                       :class="{'is-invalid': errors.has(`telefono[${i}]`)}"
                                       v-validate="{ required: true, numeric:true , min:10 }"
                                       :id="`telefono[${i}]`"
                                       :maxlength="10"/>
                                <div class="invalid-feedback" v-show="errors.has(`telefono[${i}]`)">{{ errors.first(`telefono[${i}]`) }}</div>
                            </td>

                            <td >
                                <input class="form-control"
                                       :name="`email[${i}]`"
                                       :data-vv-as="`'e-mail ${i + 1}'`"
                                       v-model="contacto.correo_electronico"
                                       :class="{'is-invalid': errors.has(`email[${i}]`)}"
                                       v-validate="{ required: true, email:true }"
                                       :id="`email[${i}]`"
                                       :maxlength="50"/>
                                <div class="invalid-feedback" v-show="errors.has(`email[${i}]`)">{{ errors.first(`email[${i}]`) }}</div>
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
                                <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarContacto(i)" :disabled="registro_proveedor.contactos.length == 1" >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-primary pull-right" :disabled="errors.count() > 0 || cargando" v-on:click="validate">
                    <span v-if="cargando">
                        <i class="fa fa-spin fa-spinner"></i>
                    </span>
                    <span v-else>
                        <i class="fa fa-save"></i> Guardar
                    </span>
                </button>
            </div>
        </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    import Treeselect from '@riophae/vue-treeselect';
    export default {
        name: "Create",
        components: {ModelListSelect, Treeselect},
        data() {
            return {
                cargando: false,
                giros : [],
                especialidades: [],
                agregar_giro : false,
                agregar_especialidad : false,
                registro_proveedor : {
                    id_especialidades:[],
                    razon_social : '',
                    rfc : '',
                    no_imss: '',
                    giro : '',
                    especialidad: '',
                    id_giro : '',
                    id_especialidad: '',
                    contactos : [
                        {
                            'nombre' : '',
                            'puesto' : '',
                            'telefono' : '',
                            'email' : '',
                            'notas' : ''
                        }
                    ]
                },
            }
        },
        mounted() {
            this.cargando = true;
            this.getGiros();
        },
        methods:{
            especialidadesAcomodar () {
                this.especialidades = this.especialidades.map(i => ({
                    id: i.id,
                    label: `${i.descripcion}`,
                    customLabel: `${i.descripcion}`,
                }));
            },
            agregarEspecialidades()
            {
                if(this.empresa.especialidades) {
                    this.empresa.especialidades.data.forEach(e => {
                        this.registro_proveedor.id_especialidades.push(e.id);
                    });
                }
            },
            agregarContacto(){
                var array = {
                    'nombre' : '',
                    'puesto' : '',
                    'telefono' : '',
                    'email' : '',
                    'notas' : ''
                }
                this.registro_proveedor.contactos.push(array);
            },
            quitarContacto(index){
                this.registro_proveedor.contactos.splice(index, 1);
            },
            changeSelectGiro(){
                if(this.registro_proveedor.id_giro == "agregar"){
                    this.agregar_giro = true;
                } else {
                    this.agregar_giro = false;
                }
            },
            changeSelectEspecialidad(){
                if(this.registro_proveedor.id_especialidad == "agregar"){
                    this.agregar_especialidad = true;
                } else {
                    this.agregar_especialidad = false;
                }
            },
            store() {
                return this.$store.dispatch('padronProveedores/empresa/revisarRFCPreexistente', this.$data.registro_proveedor)
                    .then(data => {
                        if(data['id_previo']!== undefined){
                            swal({
                                title: "Ir a expediente existente",
                                text: "El RFC ingresado pertenece a: "+data['razon_social']+".",
                                icon: "warning",
                                buttons: {
                                    cancel: {
                                        text: 'Cancelar',
                                        visible: true
                                    },
                                    confirm: {
                                        text: 'Si, Ir a Expediente',
                                        closeModal: false,
                                    }
                                }
                            }).then((value) => {
                                if(value) {
                                    this.$router.push({name: 'entrar-a-expediente', params: {id: data.id_previo}});
                                    swal.close();
                                }
                            })
                        }else{
                            return this.$store.dispatch('padronProveedores/empresa/store', this.$data.registro_proveedor)
                                .then(data => {
                                    this.$router.push({name: 'entrar-a-expediente', params: {id: data.id}});
                                }).finally( ()=>{
                                    this.cargando = false;
                                });
                        }
                    });
            },
            validate() {
                this.$validator.validate().then(result => {
                    this.registro_proveedor.razon_social = this.registro_proveedor.razon_social.toUpperCase();
                    this.registro_proveedor.rfc = this.registro_proveedor.rfc.toUpperCase();
                    if (result) {
                        this.store()
                    }
                });
            },
            getGiros() {
                return this.$store.dispatch('padronProveedores/giro/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                .then(data => {
                    this.giros = data.data;
                    if(this.giros.length>0){
                        var otro = {};
                        otro.id="agregar";
                        otro.descripcion="Agregar...";
                        this.giros.push(otro);
                    }
                })
                .finally(()=>{
                    this.getEspecialidades();
                })
            },
            getEspecialidades() {
                return this.$store.dispatch('padronProveedores/especialidad/index', {
                    params: {sort: 'descripcion', order: 'asc'}
                })
                .then(data => {
                    this.especialidades = data.data;
                    this.especialidadesAcomodar();
                })
                .finally(()=>{
                    this.cargando = false;
                })
            },
        },
    }
</script>

<style scoped>

</style>
