import ContentLoader from "react-content-loader";

const Loader = props => {
    return (
        <ContentLoader
            speed={2}
            width={400}
            height={460}
            viewBox="0 0 400 460"
            backgroundColor="#e3e3e3"
            foregroundColor="#ecebeb"
            {...props}
        >
            <circle cx="11" cy="316" r="9" />
            <rect x="25" y="313" rx="2" ry="2" width="120" height="6" />
            <rect x="0" y="60" rx="2" ry="2" width="800" height="240" />
            <circle cx="177" cy="317" r="9" />
            <rect x="191" y="314" rx="2" ry="2" width="120" height="6" />
            <circle cx="10" cy="338" r="9" />
            <rect x="26" y="335" rx="2" ry="2" width="140" height="6" />
            <rect x="5" y="367" rx="0" ry="0" width="402" height="16" />
            <rect x="5" y="391" rx="0" ry="0" width="231" height="16" />
        </ContentLoader>
    )
}

export default Loader;