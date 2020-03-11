<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                 <label><i class="fa fa-file-excel"></i> Archivo xlsx:</label>
            </div>
        </div>
        <form role="form" @submit.prevent="validate">
            <input type="file" class="form-control" id="carga_layout"
                   @change="onFileChange"
                   row="3"
                   v-validate="{ ext: ['xlsx']}"
                   name="carga_layout"
                   data-vv-as="Layout"
                   ref="carga_layout"
                   :class="{'is-invalid': errors.has('carga_layout')}"
            >
            <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (xlsx)</div>
        </form>
    </span>
</template>

<script>
    export default {
        name: "Create",
        data() {
            return {
                cargando: false,
                solicitud_partidas:[],
                resumen:[],
                file_solicitudes : null,
                file_solicitudes_name : '',
            }
        },
        methods:{
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file_solicitudes = e.target.result;
                };
                reader.readAsDataURL(file);

            },

            onFileChange(e){
                this.file_solicitudes = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.file_solicitudes_name = files[0].name;
                this.createImage(files[0]);
                setTimeout(() => {
                    this.validate()
                }, 500);
            },
            validate() {
                this.$validator.validate().then(result => {
                    if (result){
                        if(this.$refs.carga_layout.value === ''){
                            swal('¡Error!', 'Seleccione un archivo.', 'warning')
                        }else{
                            this.cargarLayout()
                        }
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file_solicitudes = null;
                        }
                        this.$validator.errors.clear();
                    }
                });
            },
            cargarLayout(){
                this.cargando = true;
                var formData = new FormData();
                formData.append('solicitud',  this.file_solicitudes);
                formData.append('nombre_archivo',  this.file_solicitudes_name);
                return this.$store.dispatch('contabilidadGeneral/solicitud-edicion-poliza/cargarLayout',
                    {
                        data: formData,
                        config: {
                            params: { _method: 'POST'}
                        }
                    })
                    .then(data => {
                        if(data.data.length > 0){
                            this.solicitud_partidas = data.data;
                            this.resumen = data.resumen;

                        }else{
                            if(this.$refs.carga_layout.value !== ''){
                                this.$refs.carga_layout.value = '';
                                this.file_solicitudes = null;
                            }
                            this.pagos = [];
                            swal('Carga Masiva', 'Archivo de layout sin cambios válidos.', 'warning')
                        }
                    }).finally(() => {
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>