export default function auth({ next, router }) {
    axios.post('/api/auth/refresh', null, {headers: {Authorization: 'Bearer ' + router.app.$session.get('jwt')}})
        .then(res => {
            router.app.$session.set('jwt', res.data.access_token);
            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + res.data.access_token;
            return next();
        })
        .catch(err => {
            router.app.$session.destroy();
            return router.push({ name: 'login' });
        });
}