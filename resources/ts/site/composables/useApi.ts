import axios from "axios";




export const client = axios.create({
    baseURL: `${import.meta.env.VITE_PUBLIC_API_URL}`,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: "application/json",
    },
});



export const useApi = () => {

    return {
        client
    }
}