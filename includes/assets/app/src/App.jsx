import { useState, useEffect } from 'react'
import api from './lib/api/api.js';

import Loaders from './components/Loaders'
import Events from './components/Events';

function App() {
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('');
    const [events, setEvents] = useState([]);

    useEffect(() => {
        api.getEvents()
            .then((d) => setEvents(d.data?.data))
            .catch(e => setError(e.message))
            .finally(() => setLoading(false));
    }, []);

    const _renderError = () => {
        return (
            <div className="_sp451--events__error">
                <p>Error: {error}</p>
            </div>
        )
    }

    const _renderData = () => {
        if (loading)
            return <Loaders />;

        if (error)
            return _renderError();

        return (
            <>
                <Events events={events} />
            </>
        )
    }

    return (
        <div className='_sp451--events'>
            <div className="_sp451--events__wrapper">
                {_renderData()}
            </div>
        </div>
    )
}

export default App
