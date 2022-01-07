<template>
    <span>
        <div class="card">
            <div class="card-body">
                <span >
                    <div class="row">
                        <div class="col-md-12">
                            <Upload v-bind:id="id" v-bind:permiso="permiso" v-bind:global="global" v-bind:base_datos="base_datos" v-bind:id_obra="id_obra"></Upload>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <List v-if="id != undefined" v-bind:id="id" v-bind:tipo="tipo" v-bind:relacionadas="relacionadas" v-bind:sin_contexto="sin_contexto"
                            v-bind:base_datos="base_datos" v-bind:id_obra="id_obra" v-bind:id_invitacion = "id_invitacion"></List>
                            <ListArchivosInvitacion v-else v-bind:id="id" v-bind:tipo="tipo" v-bind:relacionadas="relacionadas" v-bind:sin_contexto="sin_contexto"
                                  v-bind:base_datos="base_datos" v-bind:id_obra="id_obra" v-bind:id_invitacion = "id_invitacion"></ListArchivosInvitacion>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
import Upload from "./Upload";
import List from "./List";
import ListArchivosInvitacion from "./ListArchivosInvitacion";
export default {
    name: "documentos",
    props: ['id','tipo','permiso','global','relacionadas', 'sin_contexto','base_datos', "id_obra", "id_invitacion"],
    components:{ListArchivosInvitacion, List, Upload},
}
</script>

<style>

</style>
