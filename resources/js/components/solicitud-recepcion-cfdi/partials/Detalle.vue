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
                                <label >No. de Folio:</label>
                                <input class="form-control"
                                       name = "numero_folio"
                                       v-model="solicitud.numero_folio"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Fecha:</label>
                                <input class="form-control"
                                       v-model="solicitud.fecha_registro"
                                       name = "fecha_format"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Proyecto:</label>
                                <input class="form-control"
                                       name = "contacto"
                                       v-model="solicitud.obra.nombre"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Contacto HI:</label>
                                <input class="form-control"
                                       name = "contacto"
                                       v-model="solicitud.contacto"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label >Correo para recibir notificaciones:</label>
                                <input class="form-control"
                                       v-model="solicitud.correo_notificaciones"
                                       name = "correo"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label >Observaciones:</label>
                                <textarea class="form-control"
                                          v-model="solicitud.observaciones"
                                          name = "observaciones"
                                          id = "observaciones"
                                          readonly="readonly"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" >
                        <div class="col-md-2">
                            <div class="form-group">
                                <label >Estado:</label>
                                <input class="form-control"
                                       v-model="solicitud.estado_format"
                                       name = "fecha_format"
                                       readonly="readonly"
                                />
                            </div>
                        </div>
                        <template v-if="solicitud.estado == 1">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Aprobación:</label>
                                    <input class="form-control"
                                           v-model="solicitud.fecha_aprobacion"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Aprobó:</label>
                                    <input class="form-control"
                                           v-model="solicitud.usuario_aprobo"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
                                </div>
                            </div>
                        </template>
                        <template v-if="solicitud.estado == -1">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Cancelación:</label>
                                    <input class="form-control"
                                           v-model="solicitud.fecha_cancelacion"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Canceló:</label>
                                    <input class="form-control"
                                           v-model="solicitud.usuario_cancelo"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
                                </div>
                            </div>
                        </template>
                        <template v-if="solicitud.estado == -2">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Fecha Rechazo:</label>
                                    <input class="form-control"
                                           v-model="solicitud.fecha_rechazo"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Usuario Rechazo:</label>
                                    <input class="form-control"
                                           v-model="solicitud.usuario_rechazo"
                                           name = "fecha_format"
                                           readonly="readonly"
                                    />
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
