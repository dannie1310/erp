export function getLoggedinUser(){

    const userStr = localStorage.getItem('vue-session-key')

    if(!userStr){
        return null
    }

    return JSON.parse(userStr).user;
}