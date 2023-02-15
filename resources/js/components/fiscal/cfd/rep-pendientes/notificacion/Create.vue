<template>
    <span>

        <div class="card"  >
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-6">

                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-12">
                                <span><i class="fa fa-envelope"></i>DESTINATARIOS <span v-if="!cargando"><strong>{{proveedor.proveedor}}</strong></span></span>
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
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-secondary" v-on:click="salir" :disabled="cargando"><i class="fa fa-angle-left"  ></i>
                                            Regresar</button>
                                </div>

                                <div class="col-md-9">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-primary" v-on:click="actualizarContactos" :disabled="cargando" title="Actualizar Contactos"><i class="fa fa-save"  ></i>
                                            Actualizar Contactos</button>
                                        <button type="button" class="btn btn-danger" v-on:click="enviar" :disabled="errors.count() > 0 || cargando" title="Enviar Comunicado"><i class="fa fa-envelope"></i>
                                            Enviar Comunicado</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-12">
                                <span><i class="fa fa-building"></i> OBRAS RELACIONADAS</span>
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
                                            Ubicación
                                        </th>

                                        <th class="encabezado" >
                                            Responsable
                                        </th>
                                        <th class="encabezado" >
                                            Administrador
                                        </th>


                                    </tr>

                                    <tr v-for="(ubicacion, i) in this.ubicaciones">
                                        <td>{{i+1}}</td>

                                        <td>
                                            {{ ubicacion.ubicacion }}
                                        </td>

                                        <td>
                                            {{ ubicacion.correo_responsable }}
                                        </td>

                                        <td>
                                            {{ ubicacion.correo_administrador }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row">
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
            cargando : true,
            post: {},
            ubicaciones :[

            ],
            destinatarios : [
                {
                    'correo' : '',
                    'contacto' : '',
                }
            ],
        }
    },
    mounted() {
        this.getDestinatarios();
    },

    methods:{
        verPDF(){
            var url = '/api/fiscal/proveedor-rep/' + this.id + '/comunicado-pdf?&access_token=' + this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="' + url + '"  frameborder="0" height="100%" width="100%">Formato Contrato Proyectado</iframe>');
            $(this.$refs.modal).appendTo('body')
        },

        getDestinatarios(){
            let _self = this;
            return this.$store.dispatch('fiscal/proveedor-rep/find', {
                id: _self.id,
                params: {
                    "include":["contactos", "ubicaciones"]
                }
            })
                .then(data => {
                    this.$store.commit('fiscal/proveedor-rep/SET_PROVEEDOR_REP', data);

                    if(data.contactos !== undefined){
                        this.destinatarios.splice(0, 1);
                        data.contactos.data.forEach(function (contacto, i) {
                            _self.destinatarios.push({
                                'correo' : contacto.correo,
                                'contacto' : contacto.nombre,
                            })
                        });
                    }
                    if(data.ubicaciones !== undefined){
                        this.ubicaciones.splice(0, 1);
                        data.ubicaciones.data.forEach(function (ubicacion, i) {
                            _self.ubicaciones.push(ubicacion)
                        });
                    }
                })
                .finally(() => {
                    this.cargando = false;
                    this.verPDF();
                })

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
                    _self.post.destinatarios = _self.destinatarios;
                    _self.post.id = _self.id;

                    return this.$store.dispatch('fiscal/proveedor-rep/enviarInvitacion', _self.post)
                        .then((data) => {
                            this.$router.push({name: 'informe-rep-faltantes-proveedor'});
                        });
                }
            });
        },

        actualizarContactos(){
            let _self = this;
            this.$validator.validate().then(result => {
                if (result) {
                    _self.post.destinatarios = _self.destinatarios;
                    _self.post.id = _self.id;

                    return this.$store.dispatch('fiscal/proveedor-rep/actualizarContactos', _self.post)
                        .then((data) => {
                            //this.$router.push({name: 'informe-rep-faltantes-proveedor'});
                        });
                }
            });
        }
    },
    computed: {
        proveedor(){
            return this.$store.getters['fiscal/proveedor-rep/currentProveedorREP']
        },
    },
}
</script>

<style scoped>

</style>
