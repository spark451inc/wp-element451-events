import { API_OBJECT } from '../../lib/api/builder.js';
import SanitizeHTML from '../SanitizeHTML';

import Calendar from '../Icons/calendar.jsx';
import Clock from '../Icons/clock.jsx';
import Pin from '../Icons/pin.jsx';
import ArrowTopRight from '../Icons/arrow-top-right.jsx';

const Event = ({ event }) => {
    const { guid, content, allday, start_date, end_date, signups, location } = event;

    const dateFormatSameDay = (date, timeOnly = false) => {
        let _date = new Date(date);
        let options = { year: 'numeric', month: 'long', day: 'numeric' };
        let optionsTimeOnly = { hour: 'numeric', minute: 'numeric' };
        let formatter = new Intl.DateTimeFormat('en-US', !timeOnly ? options : optionsTimeOnly);
        return formatter.format(_date);
    };

    const dateFormatDifferentDay = (date1, date2) => {
        let startDate = new Date(date1);
        let endDate = new Date(date2);
        let options = { month: 'short', day: 'numeric' };
        let formatter = new Intl.DateTimeFormat('en-US', options);
        return formatter.format(startDate) + ' - ' + formatter.format(endDate);
    };

    const isSameDay = (date1, date2) => {
        let startDate = new Date(date1);
        let endDate = new Date(date2);

        return (
            startDate.getFullYear() === endDate.getFullYear() &&
            startDate.getMonth() === endDate.getMonth() &&
            startDate.getDate() === endDate.getDate()
        );
    };

    const eventUrl = () => {
        if (!API_OBJECT?.eventDomain)
            return 'javascript:void(0)';

        let slug = guid.split('.').slice(2).join('.');

        let url = `${API_OBJECT.eventDomain}/event/${slug}`;

        return url.replace(/([^:]\/)\/+/g, '$1');
    }

    let _renderTime = () => {

        if (!isSameDay(start_date, end_date))
            return null;

        return (
            <>
                <div className="_item--meta--icon">
                    <Clock />
                </div>
                <div className="_item--meta--text">
                    {allday ? 'All day' : `${dateFormatSameDay(start_date, true)} - ${dateFormatSameDay(end_date, true)}`}
                </div>
            </>
        )
    }

    const _renderDate = () => {
        let isSameDayEvent = isSameDay(start_date, end_date);

        return isSameDayEvent ? dateFormatSameDay(start_date) : dateFormatDifferentDay(start_date, end_date);
    };

    const _renderImage = () => {
        let imgUrl = API_OBJECT.plchImg;
        let transformedUrl = null;

        if (content?.image) {
            imgUrl = content.image.url;
            transformedUrl = content.image.transformations?.image_560x0_widen?.url;
        }

        return (
            <div className="_sp451--events__event--image">
                <img src={transformedUrl ? transformedUrl : imgUrl} alt={content?.title} />
            </div>
        )
    }

    return (
        <div className="_sp451--events__event">
            <a href={eventUrl()} className="_sp451--events__event--inner" target="_blank" rel="noopener noreferrer" title={`${API_OBJECT?.eventDomain ? 'Click to view event data' : ''}`}>
                <div className="_sp451--events__event--image">
                    {_renderImage()}
                </div>
                <div className="_sp451--events__event--data">
                    <div className="_sp451--events__event--meta">
                        <div className="_item--meta">
                            <div className="_item--meta--icon">
                                <Calendar />
                            </div>
                            <div className="_item--meta--text">
                                {_renderDate()}
                            </div>
                        </div>
                        <div className="_item--meta">
                            {_renderTime()}
                        </div>
                        <div className="_item--meta">
                            <div className="_item--meta--icon">
                                <Pin />
                            </div>
                            <div className="_item--meta--text">
                                {location?.venue_name}
                            </div>
                        </div>
                    </div>
                    <div className="_sp451--events__event--name">
                        {content?.title}
                    </div>
                    <div className="_sp451--events__event--description">
                        <SanitizeHTML html={content?.description} />
                    </div>
                    <div className="_sp451--events__event--link">
                        <span>Register</span>
                        <ArrowTopRight />
                    </div>
                </div>
            </a>
        </div>
    )
}

export default Event;