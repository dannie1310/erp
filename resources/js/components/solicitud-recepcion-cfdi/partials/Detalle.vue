<template>
    <span>
        <div class="card" v-if="!solicitud">
            <div class="card-body">
                <div >
                    <div class="row" >
                        <div class="col-md-12">
                            <div class="spinner-border text-success" role="status">
                               <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-if="solicitud">
            <div class="card-header">
                <h5>Datos de la Solicitud de Recepción</h5>
            </div>
            <div class="card-body">
                <span>
                    <div class="row">
                        <div class="col-md-2 offset-md-8">
                            <div class="form-group">
                                <label >Folio:</label>
                                <div>
                                    {{solicitud.numero_folio}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Fecha:</label>
                                <div>
                                    {{solicitud.fecha_registro}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <div>
                                    {{solicitud.obra.nombre}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Contacto HI:</label>
                                <div>
                                    {{solicitud.contacto}}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Correo para recibir notificaciones:</label>
                                <div>
                                    {{solicitud.correo_notificaciones}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label >Observaciones:</label>
                                <div>
                                    {{solicitud.observaciones}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" >
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Estado:</label>
                                <div>
                                    {{solicitud.estado_format}}
                                </div>
                            </div>
                        </div>
                        <template v-if="solicitud.estado == 1">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Aprobación:</label>
                                    <div>
                                        {{solicitud.fecha_aprobacion}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Aprobó:</label>
                                    <div>
                                        {{solicitud.usuario_aprobo}}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-if="solicitud.estado == -1">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Cancelación:</label>
                                    <div>
                                        {{solicitud.fecha_cancelacion}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Canceló:</label>
                                    <div>
                                        {{solicitud.usuario_cancelo}}
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-if="solicitud.estado == -2">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Rechazo:</label>
                                    <div>
                                        {{solicitud.fecha_rechazo}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Rechazo:</label>
                                    <div>
                                        {{solicitud.usuario_rechazo}}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </span>
            </div>
        </div>
        <cfdi-show v-bind:cfdi="solicitud.cfdi"></cfdi-show>
    </span>
</template>

<script>

    import CfdiShow from "../../fiscal/cfd/cfd-sat/Show";
    export default {
        name: "solicitud-recepcion-cfdi-detalle",
        components: {CfdiShow},
        props: ["solicitud"],
    }
</script>
<style>
    .dropzone-custom-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .dropzone-custom-title {
        margin-top: 0;
        color: #999;
    }

    .subtitle {
        color: #7ac142;
    }
    .vue-dropzone {
        border: 2px dashed #e5e5e5;
    }
</style>
