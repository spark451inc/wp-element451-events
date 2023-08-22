import client from './builder.js';

export default {
    getEvents: (query) => client.get('/', query),
    getFeaturedEvents: (query) => client.getFeatured('/', query),
};
