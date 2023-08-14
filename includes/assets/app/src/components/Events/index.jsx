import Event from '../Event';

// eslint-disable-next-line react/prop-types
const Events = ({ events }) => {
    // eslint-disable-next-line react/prop-types
    if (!events?.length)
        return;

    return (
        <div className="_sp451--events__wrap">
            {events.map(event => (
                <Event key={event?.guid} event={event} />
            ))}
        </div>
    )
}

export default Events;