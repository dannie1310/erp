<template>
    <span>
        <div class="card" >
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-6">

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-12">
                                <span><i class="fa fa-envelope"></i>Destinatarios</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table  table-sm table-bordered">
                                    <tr>
                                        <th class="encabezado index_corto">
                                            #
                                        </th>

                                        <th class="encabezado" >
                                            Correo
                                        </th>
                                        <th class="encabezado" >
                                            Contacto
                                        </th>

                                        <th class="encabezado icono">
                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregarDestinatario" :disabled="cargando">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-plus" v-else></i>
                                            </button>
                                        </th>
                                    </tr>

                                    <tr v-for="(destinatario, i) in this.destinatarios">
                                        <td>{{i+1}}</td>

                                        <td>
                                            <input
                                                :name="`correo_${i}`"
                                                :id="`correo_${i}`"
                                                v-model="destinatario.correo"
                                                type="text"
                                                class="form-control"
                                                v-validate="{ required: true, email:true }"
                                                :class="{'is-invalid': errors.has(`correo_${i}`)}"
                                            />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has(`correo_${i}`)">{{ errors.first(`correo_${i}`) }}</div>
                                        </td>

                                        <td>
                                            <input
                                                :name="`contacto_${i}`"
                                                :id="`contacto_${i}`"
                                                v-model="destinatario.contacto"
                                                type="text"
                                                class="form-control"
                                                v-validate="{ required: true }"
                                                :class="{'is-invalid': errors.has(`contacto_${i}`)}"
                                            />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has(`contacto_${i}`)">{{ errors.first(`contacto_${i}`) }}</div>
                                        </td>

                                        <td style="text-align: center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarDestinatario(i)" :disabled="destinatarios.length == 1" >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row" >

                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>
                                            Regresar</button>
                                        <button type="button" class="btn btn-primary" v-on:click="enviar" :disabled="errors.count() > 0 || cargando"><i class="fa fa-envelope"></i>
                                            Enviar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="modal-body modal-lg" style="height: 600px" ref="body">

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </span>
</template>

<script>
export default {
    name: "create-envio-comunicado",
    props: ['id'],
    data(){
        return {
            cargando : false,
            post: {},
            destinatarios : [
                {
                    'correo' : '',
                    'contacto' : '',
                }
            ],
        }
    },
    mounted() {
        this.verPDF();
    },
    methods:{
        verPDF(){
            var url = '/api/fiscal/proveedor-rep/' + this.id + '/comunicado-pdf?&access_token=' + this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="' + url + '"  frameborder="0" height="100%" width="100%">Formato Contrato Proyectado</iframe>');
            $(this.$refs.modal).appendTo('body')
        },

        agregarDestinatario(){
            var array = {
                'correo' : '',
                'contacto' : '',
            }
            this.destinatarios.push(array);
        },

        quitarDestinatario(index){
            this.destinatarios.splice(index, 1);
        },

        salir()
        {
            this.$router.push({name: 'informe-rep-faltantes-proveedor'});
        },

        enviar()
        {
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {
                    //console.log(_self.destinatarios);
                    //_self.post.cuerpo_correo = _self.cuerpo_correo;//
                    _self.post.destinatarios = _self.destinatarios;
                    _self.post.id = _self.id;

                    return this.$store.dispatch('fiscal/proveedor-rep/enviarInvitacion', _self.post)
                        .then((data) => {
                            this.$router.push({name: 'informe-rep-faltantes-proveedor'});
                        });
                }
            });
        },
    }
}
</script>

<style scoped>

</style>
