<template>
    <span>
        <div class="card" v-if="this.notificacion == null">
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-12">
                        <label>{{notificacion}} {{this.notificacion}}</label>
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-else>
            <div class="card-body">
                <div class="row" >
                    <div class="col-md-6">
                        <div class="row" style="margin-bottom: 5px">
                            <div class="col-md-12">
                                <span><i class="fa fa-envelope"></i>DESTINATARIOS <strong>{{notificacion.razon_social}}</strong></span>
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
                                            <button type="button" class="btn btn-sm btn-outline-success" @click="agregarDestinatario">
                                                <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                                                <i class="fa fa-plus" v-else></i>
                                            </button>
                                        </th>
                                    </tr>
                                    <tr v-for="(destinatario, i) in this.notificacion.destinatarios.data">
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
                                                v-model="destinatario.nombre"
                                                type="text"
                                                class="form-control"
                                                v-validate="{ required: true }"
                                                :class="{'is-invalid': errors.has(`contacto_${i}`)}"
                                            />
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has(`contacto_${i}`)">{{ errors.first(`contacto_${i}`) }}</div>
                                        </td>
                                        <td style="text-align: center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="quitarDestinatario(i)" :disabled="notificacion.destinatarios.length == 1" >
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
                                    <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"  ></i>
                                            Regresar</button>
                                </div>

                                <div class="col-md-9">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-primary" v-on:click="actualizarContactos" title="Actualizar Contactos"><i class="fa fa-save"  ></i>
                                            Actualizar Contactos</button>
                                        <button type="button" class="btn btn-danger" v-on:click="enviar" :disabled="errors.count() > 0 || cargando" title="Enviar Comunicado"><i class="fa fa-envelope"></i>
                                            Enviar Comunicado</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <ckeditor v-model="notificacion.cuerpo_correo" ></ckeditor>
                            </div>
                        </div>
                        <div class="row">
                            <div class="modal-body modal-lg" style="height: 600px" ref="body">
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

                                    <tr v-for="(ubicacion, i) in this.notificacion.proveedores_rep.ubicaciones.data">
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
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "notificacion-enviada-show",
    props: ['id'],
    data() {
        return{
            cargando : true,
            notificacion: null,
        }
    },
    mounted() {
      this.find();
    },
    methods: {
        find() {
            this.notificacion = null;
            this.cargando = true;
            console.log(this.notificacion)
            return this.$store.dispatch('fiscal/notificacion-rep/find', {
                id: this.id,
                params: {include: ['destinatarios', 'proveedor', 'proveedor_rep.ubicaciones']}
            }).then(data => {
                this.notificacion = data;
            }).finally(() => {
                this.cargando = false;
            })
        },
        agregarDestinatario() {
            var array = {
                'correo': '',
                'nombre': '',
            }
            this.notificacion.destinatarios.data.push(array);
        },

        quitarDestinatario(index) {
            this.notificacion.destinatarios.data.splice(index, 1);
        },
        salir() {
            this.$router.go(-1);
        },
        verPDF(){
            var url = '/api/fiscal/proveedor-rep/' + this.notificacion.proveedor.id + '/comunicado-pdf?&access_token=' + this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="' + url + '"  frameborder="0" height="100%" width="100%">Formato Contrato Proyectado</iframe>');
            $(this.$refs.modal).appendTo('body')
        },
    }
}
</script>

<style scoped>

</style>
