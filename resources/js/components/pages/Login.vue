<template>

</template>

<script>
    export default {
        data(){
            return {}
        },

        mounted() {
            if(this.$session.exists()) {
                this.$session.destroy();
            }

            let code = this.$route.query.code;
            if (!code) {
                window.location.replace('/oauth/authorize?client_id=' + process.env.MIX_CLIENT_ID + '&response_type=code&redirect_uri=' + process.env.MIX_REDIRECT_URI);
            } else {
                this.$session.destroy();
                axios
                    .post('/oauth/token', {
                        client_id: process.env.MIX_CLIENT_ID,
                        grant_type: 'authorization_code',
                        client_secret: process.env.MIX_CLIENT_SECRET,
                        redirect_uri: process.env.MIX_REDIRECT_URI,
                        code: code
                    })
                    .then(r => r.data)
                    .then(res => {
                        this.$session.start();
                        this.$session.set('jwt', res.access_token);
                        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + res.access_token;
                        this.$store.dispatch('igh/usuario/currentUser')
                            .then(data => {
                                this.$session.set('user', data.user);
                                this.$session.set('permisos_generales', data.permisos_generales);
                                this.$store.commit("auth/loginSuccess", {user: data.user, access_token: res.access_token});
                                this.$router.push({name: 'portal'});
                            })
                    })
                    .catch(error => {
                        this.$store.commit("auth/loginFailed", {error});
                    })
            }
        }
    }
</script>