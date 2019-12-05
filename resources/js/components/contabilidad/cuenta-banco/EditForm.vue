<template>
    <tr>
        <td>{{ $vnode.key + 1}}</td>
        <td>{{ cuenta.tipo.descripcion }}</td>
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
            <button class="btn btn-outline-primary btn-sm" @click="validate" :disabled="!cambio"><i class="fa fa-save"></i></button>
            <button class="btn btn-outline-danger btn-sm" @click="destroy"><i class="fa fa-trash"></i></button>
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
                    cuenta: this.cuenta.cuenta
                }
            }
        },

        computed: {
            datosContables() {
                return this.$store.getters['auth/datosContables']
            },
            cambio() {
                return (this.cuenta.cuenta != this.form.cuenta) || (this.cuenta.tipo.id != this.form.id_tipo_cuenta_banco)
            }
        },

        watch: {
            cuenta: {
                handler(cuenta) {
                    this.form.cuenta = cuenta.cuenta;
                },
                deep:true
            }
        },

        methods: {
            destroy() {
                return this.$store.dispatch('contabilidad/cuenta-banco/delete', this.cuenta.id)
                    .then(() => {
                        this.$emit('deleted');
                    })
            },
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