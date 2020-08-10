<template>
    <span>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row error-content">
                    <label for="razon_social" class="col-md-3 col-form-label">Razón Social:</label>
                    <div class="col-md-9">
                        <textarea
                                style="text-transform:uppercase;"
                                type="text"
                                name="razon_social"
                                data-vv-as="'Razón Social'"
                                v-validate="{required: true, min:6}"
                                class="form-control"
                                id="razon_social"
                                v-model="registro_proveedor.razon_social"
                                :class="{'is-invalid': errors.has('razon_social')}"></textarea>
                        <div class="invalid-feedback" v-show="errors.has('razon_social')">{{ errors.first('razon_social') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>RFC: </b> </label>
                    <div class="col-md-9">
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
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>NSS: </b> </label>
                    <div class="col-md-9">
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
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Contacto: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="contacto"
                               data-vv-as="'Contacto'"
                               v-model="registro_proveedor.nombre_contacto"
                               :class="{'is-invalid': errors.has('contacto')}"
                               v-validate="{ required: true, min:10 }"
                               id="contacto"
                               :maxlength="250"/>
                        <div class="invalid-feedback" v-show="errors.has('contacto')">{{ errors.first('contacto') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Teléfono: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="telefono"
                               data-vv-as="'Teléfono'"
                               v-model="registro_proveedor.telefono"
                               :class="{'is-invalid': errors.has('telefono')}"
                               v-validate="{ required: true, numeric:true }"
                               id="telefono"
                               :maxlength="20"/>
                        <div class="invalid-feedback" v-show="errors.has('telefono')">{{ errors.first('telefono') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Correo: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               name="correo"
                               data-vv-as="'Correo'"
                               v-model="registro_proveedor.correo_electronico"
                               :class="{'is-invalid': errors.has('correo')}"
                               v-validate="{ required: true, email:true }"
                               id="correo"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('correo')">{{ errors.first('correo') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="giros.length>0">
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Giro: </b> </label>
                    <div class="col-md-9">
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
                    </div>
                </div>
            </div>
            <div class="col-md-3" v-if="agregar_giro">
                <div class="form-group row">
                    <div class="col-md-12">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="otro_giro"
                               data-vv-as="'Giro'"
                               v-model="registro_proveedor.giro"
                               :class="{'is-invalid': errors.has('otro_giro')}"
                               v-validate="{ required: true }"
                               id="otro_giro"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('otro_giro')">{{ errors.first('otro_giro') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Giro: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="giro"
                               data-vv-as="'Giro'"
                               v-model="registro_proveedor.giro"
                               v-validate="{ required: true }"
                               :class="{'is-invalid': errors.has('giro')}"
                               id="giro"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('giro')">{{ errors.first('giro') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="especialidades.length>0">
            <div class="col-md-3" >
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Especialidad: </b> </label>
                    <div class="col-md-9">
                        <model-list-select
                                :disabled="cargando"
                                name="id_especialidad"
                                id="id_especialidad"
                                data-vv-as="'Especialidad'"
                                v-model="registro_proveedor.id_especialidad"
                                placeholder="Seleccionar o buscar especialidad"
                                option-value="id"
                                option-text="descripcion"
                                :list="especialidades"
                                v-validate="{ required: true }"
                                :onchange="changeSelectEspecialidad()"
                                :isError="errors.has(`id_especialidad`)"
                        >
                        </model-list-select>
                        <div class="invalid-feedback" v-show="errors.has('id_especialidad')">{{ errors.first('id_especialidad') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" v-if="agregar_especialidad">
                <div class="form-group row">
                    <div class="col-md-12">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="otra_especialidad"
                               data-vv-as="'Especialidad'"
                               v-model="registro_proveedor.especialidad"
                               :class="{'is-invalid': errors.has('otra_especialidad')}"
                               id="otra_especialidad"
                               v-validate="{required:true}"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('otra_especialidad')">{{ errors.first('otra_especialidad') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-3">
                <div class="form-group row">
                    <label for="rfc" class="col-md-3" ><b>Especialidad: </b> </label>
                    <div class="col-md-9">
                        <input class="form-control"
                               style="text-transform:uppercase;"
                               name="especialidad"
                               data-vv-as="'Especialidad'"
                               v-model="registro_proveedor.especialidad"
                               :class="{'is-invalid': errors.has('especialidad')}"
                               v-validate="{ required: true }"
                               id="especialidad"
                               :maxlength="50"/>
                        <div class="invalid-feedback" v-show="errors.has('especialidad')">{{ errors.first('especialidad') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
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
    export default {
        name: "Create",
        components: {ModelListSelect},
        data() {
            return {
                cargando: false,
                giros : [],
                especialidades: [],
                agregar_giro : false,
                agregar_especialidad : false,
                registro_proveedor : {
                    razon_social : '',
                    rfc : '',
                    no_imss: '',
                    nombre_contacto : '',
                    telefono : '',
                    correo_electronico : '',
                    giro : '',
                    especialidad: '',
                    id_giro : '',
                    id_especialidad: '',
                },
            }
        },
        mounted() {
            this.cargando = true;
            this.getGiros();
        },
        methods:{
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
                return this.$store.dispatch('padronProveedores/empresa/store', this.$data.registro_proveedor)
                    .then(data => {
                        this.$router.push({name: 'proveedores-edit', params: {id: data.id}});
                    }).finally( ()=>{
                        this.cargando = false;
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
                        var otro = {};
                        otro.id="agregar";
                        otro.descripcion="AGREGAR...";
                        this.giros.push(otro);
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
                        var otro = {};
                        otro.id="agregar";
                        otro.descripcion="AGREGAR...";
                        this.especialidades.push(otro);
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