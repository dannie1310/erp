export default function guest({ next, router }) {
    axios.post('/api/auth/getContext')
        .then(res => {
            console.log('success: ', res.data)
            router.app.$session.set('permisos', res.data.permisos)
            router.app.$store.commit("auth/setPermisos", res.data)
            return next();
        })
        .catch(err => {
            console.log('error: ', err.response)
            router.push({name: 'obras'});
        })
        .then(() => {
            console.log('always');
        })
}