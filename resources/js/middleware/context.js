export default function guest({ next, router }) {
    axios.post('/api/auth/getContext')
        .then(res => {
            router.app.$session.set('permisos', res.data.permisos)
            router.app.$store.commit("auth/setPermisos", res.data)
            return next();
        })
        .catch(err => {
            router.push({name: 'obras'});
        });
}