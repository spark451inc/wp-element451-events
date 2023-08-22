import { useState, useEffect } from 'react'
import api from './lib/api/api.js';
import { API_OBJECT } from './lib/api/builder.js';

import Loaders from './components/Loaders'
import Events from './components/Events';
import ArrowTopRight from "./components/Icons/arrow-top-right.jsx";

function App() {
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('');
    const [events, setEvents] = useState([]);

    useEffect(() => {
        if (!!!+API_OBJECT.showFeaturedFirst) {
            api.getEvents()
              .then((d) => setEvents(d.data?.data))
              .catch(e => setError(e.message))
              .finally(() => setLoading(false));
        } else {
            api.getFeaturedEvents()
              .then((d) => setEvents(d.data?.data))
              .then(() => {
                api.getEvents()
                  .then((d) => {
                    setEvents(prevData => {
                      const newData = d.data?.data.filter(
                        newEvent => !prevData.some(existingEvent => existingEvent.guid === newEvent.guid)
                      );

                      return [...prevData, ...newData];
                    });
                  })
                  .catch(e => setError(e.message))
                  .finally(() => setLoading(false));
              })
              .catch(e => setError(e.message))
              .finally(() => setLoading(false));
        }

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
              <Events events={events.slice(0, API_OBJECT.maxEvents)} />
              <div className="_sp451--events__link">
                  <a href={API_OBJECT.eventDomain} target="_blank">
                      <span>View All Events</span>
                      <ArrowTopRight />
                  </a>
              </div>
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
