export function getBasePath() {
    const { origin, pathname } = window.location;
    const pathParts = pathname.split('/').filter((part) => part !== '');
    const basePath = `/${pathParts[0]}/`;

    return origin + basePath;
}