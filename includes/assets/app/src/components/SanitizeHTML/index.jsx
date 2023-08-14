import sanitizeHtml from 'sanitize-html';

const defaultOptions = {
    allowedTags: [ 'b', 'i', 'em', 'strong', 'a', 'p' ],
    allowedAttributes: {
        'a': [ 'href' ]
    },
    allowedIframeHostnames: []
};

const sanitize = (dirty, options) => ({
    __html: sanitizeHtml(
        dirty,
        { ...defaultOptions, ...options }
    )
});

const SanitizeHTML = ({ html, options }) => (
    <div dangerouslySetInnerHTML={sanitize(html, options)} />
);

export default SanitizeHTML;