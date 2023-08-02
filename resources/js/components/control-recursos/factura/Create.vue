<template>
    <span>
        <div class="card" v-if="cargando">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group row error-content">
                            <label for="idserie" class="col-md-3 col-form-label">Serie:</label>
                            <div class="col-md-9">
                                <select class="form-control"
                                        data-vv-as="Serie"
                                        id="idserie"
                                        name="idserie"
                                        :error="errors.has('idserie')"
                                        v-validate="{required: true}"
                                        v-model="idserie">
                                    <option value>-- Selecionar --</option>
                                    <option v-for="(serie) in series" :value="serie.id">{{ serie.descripcion }}</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('idserie')">{{ errors.first('idserie') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group row error-content">
                            <label for="tipo" class="col-md-3 col-form-label">Tipo Factura:</label>
                            <div class="col-md-9">
                                <select class="form-control"
                                        data-vv-as="Tipo"
                                        id="tipo"
                                        name="tipo"
                                        :error="errors.has('tipo')"
                                        v-validate="{required: true}"
                                        v-model="idtipodocto">
                                    <option value>-- Selecionar --</option>
                                    <option value="1">Factura</option>
                                    <option value="6">Pago Recurrente</option>
                                </select>
                                <div style="display:block" class="invalid-feedback" v-show="errors.has('tipo')">{{ errors.first('tipo') }}</div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="col-md-6">
                         <div class="col-md-3">
                            <label for="archivo">Archivo de factura:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group error-content">
                                <input type="file" class="form-control" id="file_carga" @change="onFileChange"
                                       row="3"
                                       v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                       name="file_carga"
                                       data-vv-as="Archivo de Factura"
                                       ref="file_carga"
                                       :class="{'is-invalid': errors.has('file_carga')}">
                                <div class="invalid-feedback" v-show="errors.has('file_carga')">{{ errors.first('file_carga') }} (xml)</div>
                            </div>
                        </div>
                    </div>
                    <br />
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" v-on:click="regresar">
                        <i class="fa fa-angle-left"></i>
                        Regresar</button>
                    <button type="button" @click="validateConcurso" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
	</span>
</template>

<script>
//import {es} from "vuejs-datepicker/dist/locale";
export default {
    name: "factura-create",
    data() {
        return {
            cargando: false,
            series: [],
            idserie: '',
            idtipodocto: '',
            file_carga : null,
            file_carga_name : null,
            data: null,
        }
    },
    mounted() {
        this.$validator.reset()
        this.file_carga = null
        this.file_carga_name = null
        this.data = null
        this.getSeries();
    },
    methods : {
        getSeries() {
            this.cargando = true;
            return this.$store.dispatch('controlRecursos/serie/index', {
                params: {sort: 'descripcion', order: 'asc'}
            })
            .then(data => {
                this.series = data.data;
            })
            .finally(() => {
                this.cargando = false;
            })
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.file_carga = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        onFileChange(e){
            this.file_carga = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.file_carga_name = files[0].name;
            this.createImage(files[0]);
            if(files[0].type == "text/xml")
            {
                setTimeout(() => {
                    this.getLayoutData()
                }, 500);
            } else {
                swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
            }
        },
        getLayoutData(){
            if(this.file_carga != null && this.file_carga_name !='') {
                var formData = new FormData();
                formData.append('factura', this.file_carga);
                formData.append('nombre_archivo', this.file_carga_name);
                return this.$store.dispatch('controlRecursos/factura/cargaCFDI', {
                    data: formData, config: {params: {_method: 'POST'}}
                }).then(data => {
                    console.log(data)
                }).finally(() => {
                    this.cargando = false
                });
            }
            else{
                swal('¡Error!', 'Debe seleccionar un archivo (XML).', 'error')
            }
        },
    },
}
</script>

<style scoped>

</style>
