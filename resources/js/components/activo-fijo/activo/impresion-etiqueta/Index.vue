<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="tipo">Imprimir por: </label>
                        <select class="form-control"
                                name="tipo"
                                data-vv-as="Tipo"
                                v-model="tipo"
                                v-validate="{required: true}"
                                :error="errors.has('tipo')"
                                id="tipo">
                            <option value selected>-- Seleccione tipo de busqueda --</option>
                            <option value="1">Usuario</option>
                            <option value="2">Activo</option>
                            <option value="3">Departamento</option>
                            <option value="4">Ref. Factura</option>
                            <option value="5">Proyecto</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12" v-if="tipo == 1">
                    <div class="form-group">
                        <label for="id_sucursal">Usuario</label>
                        <select class="form-control"
                                name="id_usuario"
                                data-vv-as="Usuario"
                                v-model="id_usuario"
                                v-validate="{required: true}"
                                :error="errors.has('id_usuario')"
                                id="id_usuario">
                            <option value>-- Seleccionar--</option>
                            <option v-for="usuario in usuarios" :value="usuario.id" :disabled="usuario.id == 0 ? true : false">{{ usuario.nombre }}</option>
                        </select>
                        <div style="display:block" class="invalid-feedback" v-show="errors.has('id_usuario')">{{ errors.first('id_usuario') }}</div>
                    </div>
                </div>
                <div class="col-md-6" v-if="tipo == 2">
                    <div class="form-group error-content">
                        <div class="form-group">
                            <label for="activo">Código:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Activo"
                                   name="activo"
                                   id="activo"
                                   data-vv-as="Activo"
                                   v-validate="{required: tipo == 2 ? true : false}"
                                   v-model="activo"
                                   :class="{'is-invalid': errors.has('activo')}">
                            <div class="invalid-feedback" v-show="errors.has('activo')">{{ errors.first('activo') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" v-if="tipo == 4">
                    <div class="form-group error-content">
                        <div class="form-group">
                            <label for="referencia">Referencia de Factura:</label>
                            <input class="form-control"
                                   style="width: 100%"
                                   placeholder="Referencia"
                                   name="referencia"
                                   id="referencia"
                                   data-vv-as="Referencia"
                                   v-validate="{required: tipo == 4 ? true : false}"
                                   v-model="referencia"
                                   :class="{'is-invalid': errors.has('referencia')}">
                            <div class="invalid-feedback" v-show="errors.has('referencia')">{{ errors.first('referencia') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" v-on:click="salir"><i class="fa fa-times"></i>Cerrar</button>
            <button type="submit" class="btn btn-primary" @click="imprimir" :disabled="errors.count() > 0"><i class="fa fa-barcode"></i>Imprimir </button>
        </div>
    </div>
</template>

<script>
import UsuarioSelect from "../../../igh/usuario/Select";
export default {
    name: "Index",
    components: {UsuarioSelect},
    data() {
        return {
            tipo: '',
            id_usuario: '',
            activo : null,
            usuarios : [],
            referencia: null
        }
    },
    methods: {
        imprimir() {
           /* var datos = {
                'fecha_inicial' : this.estimacion.fecha_inicial,
                'fecha_final' : this.estimacion.fecha_final,
                'observaciones' : this.estimacion.observaciones,
                'partidas' : this.partidas
            }

            return this.$store.dispatch('contratos/estimacion/update', {
                id: this.id,
                data: datos
            })
                .then((data) => {
                    this.$router.push({name: 'estimacion'});
                })*/
        },
        salir(){
            this.$router.go(-1);
        },
        getUsuarios()
        {
            return this.$store.dispatch('activo-fijo/lista-usuario/indexOrdenado', {
                params: {
                    scope: 'partidasUbicacion'
                }
            }).then(data => {
               this.usuarios = data;
            })
        }
    },
    watch: {
        tipo(value) {
            if (value !== '' && value !== null && value !== undefined) {
                if(value == 1)
                {
                    this.getUsuarios();
                }
            }
        },
    }
}
</script>

<style scoped>

</style>
