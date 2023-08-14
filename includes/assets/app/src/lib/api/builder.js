import axios from 'axios';

const DEV = import.meta.env.DEV;

const {
    VITE_PUBLIC_MAX_EVENTS,
    VITE_PUBLIC_SHOW_FEATURED_FIRST,
    VITE_PUBLIC_CLIENT,
    VITE_PUBLIC_API_URL,
    VITE_PUBLIC_ANALYTICS_TOKEN,
    VITE_PUBLIC_FEATURE_TOKEN,
    VITE_PUBLIC_EVENT_DOMAIN,
    VITE_PUBLIC_PLCH_IMAGE
} = import.meta.env;

const DEV_API_OBJECT = {
    maxEvents: VITE_PUBLIC_MAX_EVENTS,
    showFeaturedFirst: VITE_PUBLIC_SHOW_FEATURED_FIRST,
    client: VITE_PUBLIC_CLIENT,
    apiUrl: VITE_PUBLIC_API_URL,
    analytics: VITE_PUBLIC_ANALYTICS_TOKEN,
    feature: VITE_PUBLIC_FEATURE_TOKEN,
    eventDomain: VITE_PUBLIC_EVENT_DOMAIN,
    plchImg: VITE_PUBLIC_PLCH_IMAGE,
};

const PROD_API_OBJECT = window.sp451_api_params ? {
    maxEvents: window.sp451_api_params?.sp451_max_events,
    showFeaturedFirst: window.sp451_api_params?.sp451_show_featured_first,
    client: window.sp451_api_params?.sp451_client,
    apiUrl: window.sp451_api_params?.sp451_apiUrl,
    analytics: window.sp451_api_params?.sp451_analytics,
    feature: window.sp451_api_params?.sp451_feature,
    eventDomain: window.sp451_api_params?.sp451_event_domain,
    plchImg: window.sp451_api_params?.sp451_plch_img,
} : {};

export const API_OBJECT = DEV ? DEV_API_OBJECT : PROD_API_OBJECT;

const BASE_API_URI = `https://${API_OBJECT.client}.${API_OBJECT.apiUrl}/v2/events/list?analytics=${API_OBJECT.analytics}&feature=${API_OBJECT.feature}&&embed[editor]=obj&offset=0&limit=${API_OBJECT.maxEvents}`;

const endpoint = async (path) => `${BASE_API_URI}/${path.replace(/^\/+/g, '')}`;

const config = async () => ({
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

export default {
    get: async (path, params) => axios.get(await endpoint(path = ''), Object.assign({}, await config(), { params: params })),
    post: async (path, data) => axios.post(await endpoint(path), data, await config()),
    put: async (path, data) => axios.put(await endpoint(path), data, await config()),
    delete: async (path) => axios.delete(await endpoint(path), await config()),
};
