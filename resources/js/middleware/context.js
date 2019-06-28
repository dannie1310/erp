export default function guest({ next, router }) {
    return (router.app.$session.has('obra') && router.app.$session.get('obra') != null) ? next() : router.push({ name: 'obras' });
}