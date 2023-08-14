import ContentLoader from "react-content-loader"

import Loader from './loader.jsx';

const Loaders = props => {
    return (
        <div className="_sp451--events__wrap">
            <div className="_sp451--events__event oh">
                <Loader props={props} />
            </div>
            <div className="_sp451--events__event oh">
                <Loader props={props} />
            </div>
            <div className="_sp451--events__event oh">
                <Loader props={props} />
            </div>
        </div>

    )
}

export default Loaders;