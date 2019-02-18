<template>
    <tr>
        <td>{{ $vnode.key + 1}}</td>
        <td>
            <div class="form-group error-content">
                <input
                        type="text"
                        name="cuenta"
                        data-vv-as="Cuenta"
                        v-validate="{required: true, regex: datosContables}"
                        class="form-control"
                        v-mask="{regex: datosContables}"
                        id="cuenta"
                        placeholder="Cuenta"
                        v-model="form.cuenta"
                        :class="{'is-invalid': errors.has('cuenta')}">
                <div class="invalid-feedback" v-show="errors.has('cuenta')">{{ errors.first('cuenta') }}</div>
            </div>
        </td>
        <td>
            <div class="form-group error-content">
                <select
                        class="form-control"
                        name="id_tipo_cuenta_banco"
                        id="id_tipo_cuenta_banco"
                        v-validate="{required: true}"
                        data-vv-as="Tipo de Cuenta"
                        v-model="form.id_tipo_cuenta_banco"
                        :class="{'is-invalid': errors.has('id_tipo_cuenta_banco')}">
                    >
                    <option value>-- Tipo --</option>
                    <option v-for="tipo in tipos" :value="tipo.id">{{ tipo.descripcion }}</option>
                </select>
                <div class="invalid-feedback" v-show="errors.has('id_tipo_cuenta_banco')">{{ errors.first('id_tipo_cuenta_banco') }}</div>
            </div>
        </td>
        <td>
            <button class="btn btn-primary" @click="validate" :disabled="!cambio"><i class="fa fa-save"></i></button>
        </td>
    </tr>
</template>

<script>
    export default {
        name: "cuenta-banco-edit-form",
        props: ['cuenta'],
        data() {
            return {
                form: {
                    cuenta: this.cuenta.cuenta,
                    id_tipo_cuenta_banco: this.cuenta.id_tipo_cuenta_banco
                }
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            tipos() {
                return this.$store.getters['contabilidad/tipo-cuenta-bancos/tipos']
            },
            cambio() {
                return (this.cuenta.cuenta != this.form.cuenta) || (this.cuenta.id_tipo_cuenta_banco != this.form.id_tipo_cuenta_banco)
            }
        },

        watch: {
            cuenta: {
                handler(cuenta) {
                    this.form.cuenta = cuenta.cuenta;
                    this.form.id_tipo_cuenta_banco = cuenta.id_tipo_cuenta_banco;
                },
                deep:true
            }
        },

        methods: {
            update() {
                return this.$store.dispatch('contabilidad/cuenta-banco/update', {
                    id: this.cuenta.id,
                    data: this.form
                })
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            }
        }
    }
</script>