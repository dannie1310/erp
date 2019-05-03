export function getSistemas(){

    const session = localStorage.getItem('vue-session-key')

    if(!session){
        return []
    }

    return JSON.parse(session).sistemas;
}