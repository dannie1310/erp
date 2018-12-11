export default function guest({ next, router }) {
    axios.post('/api/auth/getContext')
        .then(res => {
            return next();
        })
        .catch(err => {
            router.push({name: 'obras'});
        });
}