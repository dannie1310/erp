<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="archivo">Archivo:</label>
                        </div>
                        <div class="col-md-10">
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
            <div class="row" v-if="datos != null">
                <div class="col-md-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                            <tr>
                                <th class="encabezado" colspan="2">RFC</th>
                                <th class="encabezado">Mes</th>
                                <th class="encabezado">Año</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="2">{{datos.rfc}}</td>
                                <td class="numerico" style="text-align: right">{{datos.mes}}</td>
                                <td class="numerico" style="text-align: right">{{datos.anio}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table">
                            <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th class="encabezado">Código cuenta</th>
                                <th class="encabezado">Cuenta</th>
                                <th class="encabezado">Naturaleza</th>
                                <th class="encabezado">Saldo Inicial</th>
                                <th class="encabezado">Debe</th>
                                <th class="encabezado">Haber</th>
                                <th class="encabezado">Saldo Final</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(partida, i) in datos.partidas">
                                <td class="encabezado index_corto">{{i+1}}</td>
                                <td class="numerico">{{partida.codigo_cuenta}}</td>
                                <td class="numerico">{{partida.numero_cuenta}}</td>
                                <td class="numerico">{{partida.naturaleza}}</td>
                                <td class="numerico" style="text-align: right">{{partida.saldo}}</td>
                                <td class="numerico" style="text-align: right">{{partida.debe}}</td>
                                <td class="numerico" style="text-align: right">{{partida.haber}}</td>
                                <td class="numerico" style="text-align: right">{{partida.saldo_total}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
            datos : null
        }
    },
    methods: {
        onFileChange(e){
            this.archivo = null;
            this.archivo_name = null;
            this.datos = null;
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
                        this.datos = data
                    }else{
                        if(this.$refs.archivo.value !== ''){
                            this.$refs.archivo.value = '';
                            this.archivo = null;
                        }
                        swal('Contabilidad Electrónica con XML', 'El archivo debe ser compatible con la contabilidad electrónica.', 'warning')
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
