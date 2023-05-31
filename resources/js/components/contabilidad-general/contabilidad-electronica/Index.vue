<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="archivo">Archivo:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group error-content" >
                                <input type="file" class="form-control" id="archivo" @change="onFileChange"
                                       row="3"
                                       v-validate="{required: true,  ext: ['xml'], size: 3072}"
                                       name="archivo"
                                       data-vv-as="Archivo"
                                       ref="archivo"
                                       :class="{'is-invalid': errors.has('archivo')}">
                                <div class="invalid-feedback" v-show="errors.has('archivo')">{{ errors.first('archivo') }} (xml)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-primary" :disabled="errors.count() > 0 ">Procesar</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Index",
    data() {
        return {
            cargando :false,
            archivo:null,
            archivo_name:null,
        }
    },
    methods: {
        onFileChange(e){
            this.archivo = null;
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.archivo_name = files[0].name;
            this.createImage(files[0], 1);

            if(files[0].type == "text/xml")
            {
                setTimeout(() => {
                    this.cargarXML()
                }, 500);
            } else {
                swal('Carga con XML', 'El archivo debe ser en formato XML', 'error')
            }
        },
        createImage(file) {
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.archivo = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        cargarXML(){
            this.cargando = true;
            var formData = new FormData();
            formData.append('xml',  this.archivo);
            formData.append('nombre_archivo',  this.archivo_name);

            return this.$store.dispatch('contabilidadGeneral/contabilidad-electronica/cargarXML',
                {
                    data: formData,
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    var count = Object.keys(data).length;

                    if(count > 0 ){
                        if(data.tipo_comprobante === "I"){

                            this.dato.total = (parseFloat(this.dato.total) + parseFloat(data.total)).toFixed(2);
                            this.dato.impuesto = (parseFloat(this.dato.impuesto) + parseFloat(data.impuesto)).toFixed(2);
                            this.dato.referencia = data.serie + data.folio;
                            this.dato.emision = data.fecha;
                            this.dato.id_empresa = data.empresa_bd.id_empresa;
                            this.dato.id_moneda = data.moneda_bd.id_moneda;
                            this.empresas.push({id:data.empresa_bd.id_empresa,razon_social:data.empresa_bd.razon_social,rfc:data.empresa_bd.rfc});
                        } else if(data.tipo_comprobante === "E"){
                            this.dato.total = (parseFloat(this.dato.total) - parseFloat(data.total)).toFixed(2);
                        }


                    }else{
                        if(this.$refs.archivo.value !== ''){
                            this.$refs.archivo.value = '';
                            this.dato.archivo = null;
                        }
                        this.cleanData();
                        swal('Carga con XML', 'Archivo sin datos válidos', 'warning')
                    }
                }).finally(() => {
                    this.cargando = false;
                });
        },
    },
    watch: {

    }
}
</script>

<style scoped>

</style>
