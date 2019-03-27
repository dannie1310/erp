import {getSistemas} from "../store/modules/partials/sistemas";

export default function access({ from, next, router }) {
    const sistemas = getSistemas();

    var sistema = sistemas.find((sistema, i) => {
        return sistema.url === router.currentRoute.name;
    });

    return sistema ? next() : next(from.path);
}