<template xmlns="http://www.w3.org/1999/html">
    <span>
        <div class="card" v-if="this.notificacion == null">
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
                                    </tr>
                                    <tr v-for="(destinatario, i) in this.notificacion.destinatarios.data">
                                        <td>{{i+1}}</td>
                                        <td>{{destinatario.correo}}</td>
                                        <td>{{destinatario.nombre}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div v-html="notificacion.cuerpo_correo" style="border: black 2px solid; padding: 10px;">
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
                                    <tr v-for="(ubicacion, i) in notificacion.proveedor_rep.ubicaciones.data">
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
            <div class="card-footer">
                <div class="row" >
                    <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-angle-left"></i>
                            Regresar</button>
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
            return this.$store.dispatch('fiscal/notificacion-rep/find', {
                id: this.id,
                params: {include: ['destinatarios', 'proveedor', 'proveedor_rep.ubicaciones']}
            }).then(data => {
                this.notificacion = data;
                this.verPDF();
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
            var url = '/api/fiscal/notificacion_rep/' + this.notificacion.id + '/pdf?&access_token=' + this.$session.get('jwt');
            $(this.$refs.body).html('<iframe src="' + url + '"  frameborder="0" height="100%" width="100%">Formato Notificación</iframe>');
            $(this.$refs.modal).appendTo('body')
        },
    }
}
</script>

<style scoped>

</style>
