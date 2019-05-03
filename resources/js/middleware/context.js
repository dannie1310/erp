export default function guest({ next, router }) {
    return router.app.$session.has('obra') ? next() : router.push({ name: 'obras' });
}